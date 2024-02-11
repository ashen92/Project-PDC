<?php
declare(strict_types=1);

namespace App\Services;

use App\Constant\Constants;
use App\DTOs\CreateCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\Student;
use App\Entities\UserGroup;
use App\Exceptions\UserExistsException;
use App\Interfaces\IEmailService;
use App\Models\InternshipCycle;
use App\Repositories\InternshipProgramRepository;
use App\Repositories\UserRepository;
use App\Security\Role;
use DateTimeImmutable;
use Throwable;

readonly class InternshipProgramService
{
    public function __construct(
        private InternshipProgramRepository $internshipProgramRepository,
        private UserRepository $userRepository,
        private UserService $userService,
    ) {
    }

    /**
     * @return array<UserGroup>
     */
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

    /**
     * @return array<UserGroup>
     */
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

    public function getLatestCycle(): ?InternshipCycle
    {
        return $this->internshipProgramRepository->findLatestCycle();
    }

    public function createCycle(CreateCycleDTO $dto): bool
    {
        $this->internshipProgramRepository->beginTransaction();
        try {
            $cycleId = $this->internshipProgramRepository->createCycle();

            $partnerGroup = $this->userRepository
                ->createUserGroup(
                    Constants::AUTO_GENERATED_USER_GROUP_PREFIX->value .
                    "InternshipCycle-{$cycleId}-Partners"
                );
            $studentGroup = $this->userRepository
                ->createUserGroup(
                    Constants::AUTO_GENERATED_USER_GROUP_PREFIX->value .
                    "InternshipCycle-{$cycleId}-Students"
                );

            $this->userRepository
                ->addRoleToUserGroup($partnerGroup->getId(), Role::InternshipProgram_Partner_Admin);
            $this->userRepository
                ->addRoleToUserGroup($studentGroup->getId(), Role::InternshipProgram_Student);

            $this->userRepository
                ->addUsersToUserGroup($partnerGroup->getId(), $dto->partnerGroup);
            $this->userRepository
                ->addUsersToUserGroup($studentGroup->getId(), $dto->studentGroup);

            $this->internshipProgramRepository->updateCycleUserGroups(
                $cycleId,
                [$partnerGroup->getId()],
                $studentGroup->getId(),
            );

            $this->internshipProgramRepository->commit();
            return true;

        } catch (Throwable $th) {
            $this->internshipProgramRepository->rollBack();
            throw $th;
        }
    }

    /**
     * @throws Throwable
     */
    public function endInternshipCycle(): bool
    {
        $this->internshipProgramRepository->beginTransaction();
        try {
            $cycle = $this->internshipProgramRepository->findLatestActiveCycle();
            $this->internshipProgramRepository->endCycle();

            $this->internshipProgramRepository->removeRolesFromUserGroups(
                [
                    Role::InternshipProgram_Partner_Admin,
                    Role::InternshipProgram_Partner
                ],
                $cycle->getPartnerGroupIds(),
            );

            $this->internshipProgramRepository->removeRolesFromUserGroups(
                [Role::InternshipProgram_Student],
                [$cycle->getStudentGroupId()],
            );

            $this->internshipProgramRepository->commit();
            return true;
        } catch (Throwable $th) {
            $this->internshipProgramRepository->rollBack();
            throw $th;
        }
    }

    /**
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createManagedUser(int $managedBy, CreateUserDTO $userDTO): void
    {
        $userId = $this->userService->createUser($userDTO);

        $this->userService->managePartner($managedBy, $userId);

        $groupName = Constants::AUTO_GENERATED_USER_GROUP_PREFIX->value . "Users-Managed-By-$managedBy";
        $group = $this->userRepository->findUserGroupByName($groupName);

        if (!$group) {
            $group = $this->userRepository->createUserGroup($groupName);
            $this->userRepository->addRoleToUserGroup($group->getId(), Role::InternshipProgram_Partner);
        }

        $this->userRepository->addToUserGroup($userId, $group->getId());
    }
}