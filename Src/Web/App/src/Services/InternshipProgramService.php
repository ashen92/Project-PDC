<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Models\InternshipCycle;
use App\Models\UserGroup;
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
            if (str_contains(strtolower($group->getName()), 'admin')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'coordinator')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'partner')) {
                continue;
            }
            if (str_starts_with($group->getName(), UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX)) {
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
            if (str_contains(strtolower($group->getName()), 'admin')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'coordinator')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'student')) {
                continue;
            }
            if (str_starts_with($group->getName(), UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX)) {
                continue;
            }
            $eligibleGroups[] = $group;
        }
        return $eligibleGroups;
    }

    public function getParticipants(int $cycleId, int $limit, int $offsetBy): array
    {
        $result['totalCount'] = $this->internshipProgramRepository->countParticipants($cycleId);
        $result['data'] = $this->internshipProgramRepository->findParticipants(
            $cycleId,
            $limit,
            $offsetBy
        );
        return $result;
    }

    public function getLatestInternshipCycleId(): ?int
    {
        return $this->getLatestCycle()?->getId();
    }

    public function getLatestCycle(): ?InternshipCycle
    {
        return $this->internshipProgramRepository->findLatestCycle();
    }

    public function createCycle(int $partnerGroupId, int $studentGroupId): bool
    {
        $this->internshipProgramRepository->beginTransaction();
        try {
            $cycleId = $this->internshipProgramRepository->createCycle();

            $partnerGroup = $this->userRepository
                ->createUserGroup(
                    UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX .
                    "InternshipCycle-{$cycleId}-Partners"
                );
            $studentGroup = $this->userRepository
                ->createUserGroup(
                    UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX .
                    "InternshipCycle-{$cycleId}-Students"
                );

            $this->userRepository
                ->addRoleToUserGroup($partnerGroup->getId(), Role::InternshipProgramPartnerAdmin);
            $this->userRepository
                ->addRoleToUserGroup($studentGroup->getId(), Role::InternshipProgramStudent);

            $this->userRepository
                ->addUsersToUserGroup($partnerGroup->getId(), $partnerGroupId);
            $this->userRepository
                ->addUsersToUserGroup($studentGroup->getId(), $studentGroupId);

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
                    Role::InternshipProgramPartnerAdmin,
                    Role::InternshipProgramPartner
                ],
                $cycle->getPartnerGroupIds(),
            );

            $this->internshipProgramRepository->removeRolesFromUserGroups(
                [Role::InternshipProgramStudent],
                [$cycle->getStudentGroupId()],
            );

            $this->internshipProgramRepository->commit();
            return true;
        } catch (Throwable $th) {
            $this->internshipProgramRepository->rollBack();
            throw $th;
        }
    }

    public function modifyInternshipCycleDates(
        int $cycleId,
        ?DateTimeImmutable $jobCollectionStart = null,
        ?DateTimeImmutable $jobCollectionEnd = null,
        ?DateTimeImmutable $jobHuntRound1Start = null,
        ?DateTimeImmutable $jobHuntRound1End = null,
        ?DateTimeImmutable $jobHuntRound2Start = null,
        ?DateTimeImmutable $jobHuntRound2End = null,
    ): void {
        $this->internshipProgramRepository->modifyInternshipCycleDates(
            $cycleId,
            $jobCollectionStart,
            $jobCollectionEnd,
            $jobHuntRound1Start,
            $jobHuntRound1End,
            $jobHuntRound2Start,
            $jobHuntRound2End,
        );
    }

    public function resetInternshipCycleDates(
        int $cycleId,
        bool $resetJobCollectionEnd = false,
        bool $resetJobHuntRound1End = false,
        bool $resetJobHuntRound2End = false,
    ): void {
        $this->internshipProgramRepository->resetInternshipCycleDates(
            $cycleId,
            false,
            $resetJobCollectionEnd,
            false,
            $resetJobHuntRound1End,
            false,
            $resetJobHuntRound2End,
        );
    }

    /**
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createManagedUser(int $managedBy, CreateUserDTO $userDTO): void
    {
        $userId = $this->userService->createUser($userDTO);

        $this->userService->managePartner($managedBy, $userId);

        $groupName = UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX . "Users-Managed-By-$managedBy";
        $group = $this->userRepository->findUserGroupByName($groupName);

        if (!$group) {
            $group = $this->userRepository->createUserGroup($groupName);
            $this->userRepository->addRoleToUserGroup($group->getId(), Role::InternshipProgramPartner);
        }

        $this->userRepository->addToUserGroup($userId, $group->getId());
    }
}