<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateInternshipCycleDTO;
use App\Entities\InternshipCycle;
use App\Entities\Role;
use App\Entities\UserGroup;
use App\Interfaces\IInternshipCycleService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InternshipCycleService implements IInternshipCycleService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getEligibleStudentGroupsForInternshipCycle(): array
    {
        $groups = $this->entityManager->getRepository(UserGroup::class)->findAll();
        $eligibleGroups = [];
        foreach ($groups as $group) {
            if (str_contains(strtolower($group->getName()), "admin")) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), "coordinator")) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), "partner")) {
                continue;
            }
            if (str_starts_with($group->getName(), "InternshipCycle-")) {
                continue;
            }
            $eligibleGroups[] = $group;
        }
        return $eligibleGroups;
    }

    public function getEligiblePartnerGroupsForInternshipCycle(): array
    {
        $groups = $this->entityManager->getRepository(UserGroup::class)->findAll();
        $eligibleGroups = [];
        foreach ($groups as $group) {
            if (str_contains(strtolower($group->getName()), "admin")) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), "coordinator")) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), "student")) {
                continue;
            }
            if (str_starts_with(strtolower($group->getName()), "InternshipCycle-")) {
                continue;
            }
            $eligibleGroups[] = $group;
        }
        return $eligibleGroups;
    }

    public function getLatestInternshipCycleId(): ?int
    {
        $latestInternshipCycle = $this->entityManager
            ->getRepository(InternshipCycle::class)
            ->findBy([], ["createdAt" => "DESC"], 1);
        return $latestInternshipCycle[0] ? $latestInternshipCycle[0]->getId() : null;
    }

    public function getLatestInternshipCycle(): ?InternshipCycle
    {
        $latestInternshipCycle = $this->entityManager
            ->getRepository(InternshipCycle::class)
            ->findBy([], ["createdAt" => "DESC"], 1);

        if (empty($latestInternshipCycle)) {
            return null;
        }

        return $latestInternshipCycle[0];
    }

    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle
    {
        $internshipCycle = new InternshipCycle();
        $this->entityManager->persist($internshipCycle);
        $this->entityManager->flush();

        $partnerGroup = new UserGroup("InternshipCycle-{$internshipCycle->getId()}-Partners");
        $studentGroup = new UserGroup("InternshipCycle-{$internshipCycle->getId()}-Students");

        $roleInternshipPartner = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(
                ["name" => "ROLE_INTERNSHIP_PARTNER"]
            );
        $roleInternshipPartner->addGroup($partnerGroup);

        $roleInternshipStudent = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(
                ["name" => "ROLE_INTERNSHIP_STUDENT"]
            );
        $roleInternshipStudent->addGroup($studentGroup);

        $partnerGroup->addUsersFrom(
            $this->entityManager
                ->getRepository(UserGroup::class)
                ->findOneBy(
                    ["name" => $createInternshipCycleDTO->partnerGroup]
                )
        );

        $studentGroup->addUsersFrom(
            $this->entityManager
                ->getRepository(UserGroup::class)
                ->findOneBy(
                    ["name" => $createInternshipCycleDTO->studentGroup]
                )
        );

        $internshipCycle->setCollectionStartDate(new DateTime($createInternshipCycleDTO->collectionStartDate));
        $internshipCycle->setCollectionEndDate(new DateTime($createInternshipCycleDTO->collectionEndDate));
        $internshipCycle->setApplicationStartDate(new DateTime($createInternshipCycleDTO->applicationStartDate));
        $internshipCycle->setApplicationEndDate(new DateTime($createInternshipCycleDTO->applicationEndDate));
        $internshipCycle->setPartnerGroup($partnerGroup);
        $internshipCycle->setStudentGroup($studentGroup);

        $this->entityManager->persist($partnerGroup);
        $this->entityManager->persist($studentGroup);
        $this->entityManager->flush();

        return $internshipCycle;
    }

    /**
     * @return array An array of App\Entities\User objects
     */
    public function getStudentUsers(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null) {
            $internshipCycleId = $this->getLatestInternshipCycleId();
        }

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\User', 'u');

        $queryBuilder = $this->entityManager->createNativeQuery(
            "SELECT u.*
            FROM user_groups ug
            JOIN user_group_membership ugm ON ug.id = ugm.usergroup_id
            JOIN users u ON ugm.user_id = u.id
            WHERE ug.id = (
                SELECT student_group_id
                FROM internship_cycles
                WHERE id = :internshipCycleId
            )",
            $rsm
        );

        $queryBuilder->setParameter("internshipCycleId", $internshipCycleId);
        return $queryBuilder->getResult();
    }

    public function endInternshipCycle(?int $id = null): bool
    {
        $internshipCycle = null;
        if ($id === null) {
            $internshipCycle = $this->getLatestInternshipCycle();
        } else {
            $internshipCycle = $this->entityManager
                ->getRepository(InternshipCycle::class)
                ->find($id);
        }

        if ($internshipCycle === null) {
            return false;
        }

        $internshipCycle->end();

        $roleInternshipPartner = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(
                ["name" => "ROLE_INTERNSHIP_PARTNER"]
            );

        $roleInternshipPartner->removeGroup($internshipCycle->getPartnerGroup());

        $roleInternshipStudent = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(
                ["name" => "ROLE_INTERNSHIP_STUDENT"]
            );
        $roleInternshipStudent->removeGroup($internshipCycle->getStudentGroup());

        $this->entityManager->persist($internshipCycle);
        $this->entityManager->flush();
        return true;
    }
}