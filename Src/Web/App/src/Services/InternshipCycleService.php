<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateInternshipCycleDTO;
use App\Entities\InternshipCycle;
use App\Interfaces\IInternshipCycleService;
use App\Repositories\InternshipCycleRepository;
use App\Repositories\UserRepository;
use DateTime;

class InternshipCycleService implements IInternshipCycleService
{
    public function __construct(
        private InternshipCycleRepository $internshipCycleRepository,
        private UserRepository $userRepository
    ) {
    }

    public function getEligibleStudentGroupsForInternshipCycle(): array
    {
        $groups = $this->userRepository->findAllUserGroups();
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
        $groups = $this->userRepository->findAllUserGroups();
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
            if (str_starts_with($group->getName(), "InternshipCycle-")) {
                continue;
            }
            $eligibleGroups[] = $group;
        }
        return $eligibleGroups;
    }

    public function getLatestInternshipCycleId(): ?int
    {
        return $this->getLatestInternshipCycle()?->getId();
    }

    public function getLatestInternshipCycle(): ?InternshipCycle
    {
        return $this->internshipCycleRepository->findBy([], ["createdAt" => "DESC"], 1)[0] ?? null;
    }

    public function getLatestActiveInternshipCycle(): ?InternshipCycle
    {
        return $this->internshipCycleRepository->findBy(["endedAt" => null], ["createdAt" => "DESC"], 1)[0] ?? null;
    }

    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle
    {
        $internshipCycle = new InternshipCycle();
        $this->internshipCycleRepository->save($internshipCycle);

        $partnerGroup = $this->userRepository
            ->addUserGroup("InternshipCycle-{$internshipCycle->getId()}-Partners");
        $studentGroup = $this->userRepository
            ->addUserGroup("InternshipCycle-{$internshipCycle->getId()}-Students");

        $this->userRepository
            ->addRoleToUserGroup($partnerGroup->getId(), "ROLE_INTERNSHIP_PARTNER");
        $this->userRepository
            ->addRoleToUserGroup($studentGroup->getId(), "ROLE_INTERNSHIP_STUDENT");

        $this->userRepository
            ->addUsersToUserGroup($partnerGroup->getId(), $createInternshipCycleDTO->partnerGroup);
        $this->userRepository
            ->addUsersToUserGroup($studentGroup->getId(), $createInternshipCycleDTO->studentGroup);

        $internshipCycle->setCollectionStartDate(
            new DateTime($createInternshipCycleDTO->collectionStartDate)
        );
        $internshipCycle->setCollectionEndDate(
            new DateTime($createInternshipCycleDTO->collectionEndDate)
        );
        $internshipCycle->setApplicationStartDate(
            new DateTime($createInternshipCycleDTO->applicationStartDate)
        );
        $internshipCycle->setApplicationEndDate(
            new DateTime($createInternshipCycleDTO->applicationEndDate)
        );
        $internshipCycle->setPartnerGroup($partnerGroup);
        $internshipCycle->setStudentGroup($studentGroup);

        $this->internshipCycleRepository->save($internshipCycle);

        return $internshipCycle;
    }

    public function getStudentUsers(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null) {
            $internshipCycleId = $this->getLatestInternshipCycleId();
        }

        if ($internshipCycleId === null) {
            return [];
        }

        return $this->internshipCycleRepository->findStudentUsers($internshipCycleId);
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

        $internshipCycle->setEndedAt(new DateTime("now"));

        $this->userRepository->removeRoleFromUserGroup(
            $internshipCycle->getPartnerGroup()->getId(),
            "ROLE_INTERNSHIP_PARTNER"
        );

        $this->userRepository->removeRoleFromUserGroup(
            $internshipCycle->getStudentGroup()->getId(),
            "ROLE_INTERNSHIP_STUDENT"
        );

        $this->internshipCycleRepository->save($internshipCycle);
        return true;
    }
}