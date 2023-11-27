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
use Symfony\Contracts\Cache\CacheInterface;

class InternshipCycleService implements IInternshipCycleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
    }

    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle
    {
        $internshipCycle = new InternshipCycle();
        $this->entityManager->persist($internshipCycle);
        $this->entityManager->flush();

        $partnerGroup = new UserGroup("Cycle_{$internshipCycle->getId()}-Partner");
        $studentGroup = new UserGroup("Cycle_{$internshipCycle->getId()}-Student");

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
}