<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\InternshipCycle;
use App\Interfaces\IEmailService;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IUserService;
use App\Repositories\InternshipProgramRepository;
use App\Repositories\UserRepository;
use App\Security\Role;

class InternshipCycleService implements IInternshipCycleService
{
    public function __construct(
        private InternshipProgramRepository $internshipProgramRepository,
        private UserRepository $userRepository,
        private IUserService $userService,
        private IEmailService $emailService
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
        return $this->getLatestCycle()?->getId();
    }

    public function getLatestCycle(): ?\App\Models\InternshipCycle
    {
        return $this->internshipProgramRepository->findLatestCycle();
    }

    public function getLatestActiveCycle(): ?\App\Models\InternshipCycle
    {
        return $this->internshipProgramRepository->findLatestActiveCycle();
    }

    public function createCycle(CreateCycleDTO $dto): \App\Models\InternshipCycle
    {
        $this->internshipProgramRepository->beginTransaction();
        try {
            $cycle = $this->internshipProgramRepository->createCycle();

            $partnerGroup = $this->userRepository
                ->createUserGroup("InternshipCycle-{$cycle->getId()}-Partners");
            $studentGroup = $this->userRepository
                ->createUserGroup("InternshipCycle-{$cycle->getId()}-Students");

            $this->userRepository
                ->addRoleToUserGroup($partnerGroup->getId(), Role::InternshipProgram_Partner_Admin);
            $this->userRepository
                ->addRoleToUserGroup($studentGroup->getId(), Role::InternshipProgram_Student);

            $this->userRepository
                ->addUsersToUserGroup($partnerGroup->getId(), $dto->partnerGroup);
            $this->userRepository
                ->addUsersToUserGroup($studentGroup->getId(), $dto->studentGroup);

            $cycle->setCollectionStartDate(
                new \DateTimeImmutable($dto->collectionStartDate)
            );
            $cycle->setCollectionEndDate(
                new \DateTimeImmutable($dto->collectionEndDate)
            );
            $cycle->setApplicationStartDate(
                new \DateTimeImmutable($dto->applicationStartDate)
            );
            $cycle->setApplicationEndDate(
                new \DateTimeImmutable($dto->applicationEndDate)
            );
            $cycle->setPartnerGroupId($partnerGroup->getId());
            $cycle->setStudentGroupId($studentGroup->getId());

            $this->internshipProgramRepository->updateCycle($cycle);

            $this->internshipProgramRepository->commit();
            return $cycle;

        } catch (\Throwable $th) {
            $this->internshipProgramRepository->rollBack();
            throw $th;
        }
    }

    public function getStudentUsers(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null) {
            $internshipCycleId = $this->getLatestInternshipCycleId();
        }

        if ($internshipCycleId === null) {
            return [];
        }

        return $this->internshipProgramRepository->findStudents($internshipCycleId);
    }

    public function endInternshipCycle(?int $id = null): bool
    {
        if ($id === null) {
            $cycle = $this->getLatestCycle();
        } else {
            $cycle = $this->internshipProgramRepository->findCycle($id);
        }

        if ($cycle === null) {
            return false;
        }

        $cycle->end();

        $this->userRepository->removeRoleFromUserGroup(
            $cycle->getPartnerGroupId(),
            Role::InternshipProgram_Partner_Admin
        );

        $this->userRepository->removeRoleFromUserGroup(
            $cycle->getStudentGroupId(),
            Role::InternshipProgram_Student
        );

        $this->internshipProgramRepository->updateCycle($cycle);
        return true;
    }

    #[\Override] public function createManagedUser(int $ownerId, CreateUserDTO $userDTO): void
    {
        $owner = $this->userRepository->find($ownerId);

        $user = $this->userService->createUser($userDTO);

        $owner->addToManage($user);

        $this->userRepository->save($owner);
        $this->userRepository->save($user);

        $userGroupName = "{$user->getId()}-managed-users";
        $userGroup = $this->userRepository->findUserGroupByName($userGroupName);

        if (!$userGroup) {
            $userGroup = $this->userRepository->createUserGroup($userGroupName);
            $this->userRepository->addRoleToUserGroup($userGroup->getId(), Role::InternshipProgram_Partner);
        }

        $this->userRepository->addToUserGroup($user->getId(), $userGroup->getId());
    }
}