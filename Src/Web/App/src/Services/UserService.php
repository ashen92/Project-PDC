<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Interfaces\IEmailService;
use App\Interfaces\IPasswordHasher;
use App\Models\Partner;
use App\Models\User;
use App\Models\UserInviteEmail;
use App\Repositories\UserRepository;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private IPasswordHasher $passwordHasher,
        private IEmailService $emailService
    ) {
    }

    /**
     * @return int The ID of the created user
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createUser(CreateUserDTO $userDTO): int
    {
        if (
            $this->userRepository->doesUserExist(
                $userDTO->email ?? $userDTO->studentEmail,
                !$userDTO->email
            )
        ) {
            throw new UserExistsException();
        }

        $userId = $this->userRepository->createUser($userDTO);

        if ($userDTO->userType != "student" || ($userDTO->userType == "student" && $userDTO->sendEmail !== null)) {

            if ($userDTO->userType == "student") {
                $user = $this->userRepository->findStudent($userId);
                $mail = new UserInviteEmail(
                    $user->getStudentEmail(),
                    $user->getFullName(),
                    $user->generateActivationToken()
                );
            } else {
                $user = $this->userRepository->findUser($userId);
                $mail = new UserInviteEmail(
                    $user->getEmail(),
                    $user->getFirstName(),
                    $user->generateActivationToken()
                );
            }

            $this->userRepository->updateUser($user);

            $this->emailService->sendEmail($mail);
        }
        return $userId;
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findUser($id);
    }

    /**
     * @return array<Partner>
     */
    public function getManagedUsers(int $userId): array
    {
        return $this->userRepository->findManagedUsers($userId);
    }

    public function searchUsers(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchUsers($numberOfResults ?? 50, $offsetBy ?? 0);
    }



    public function searchGroups(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchGroups($numberOfResults, $offsetBy);
    }

    public function managePartner(int $managedBy, int $partnerId): bool
    {
        return $this->userRepository->managePartner($managedBy, $partnerId);
    }

    public function findActiveUsers(): array
    {
        return $this->userRepository->findActiveUsers();
    }

    public function countActiveUsers(): int
    {
        return count($this->userRepository->findActiveUsers());
    }

    public function findStudentUsers(): array
    {
        return $this->userRepository->findStudentUsers();
    }

    public function countStudentUsers(): int
    {
        return count($this->userRepository->findStudentUsers());
    }

    public function findCoordinators(): array
    {
        return $this->userRepository->findCoordinators();
    }

    public function countCoordinators(): int
    {
        return count($this->userRepository->findCoordinators());
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function activateUser(int $id): bool
    {
        return $this->userRepository->activate($id);
    }

    public function deactivateUser(int $id): bool
    {
        return $this->userRepository->deactivate($id);
    }

    public function addUserGroupMember(int $userid, int $groupid): bool
    {
        $res = $this->userRepository->checkUserGroupMember($userid, $groupid);
        if ($res) {
            $this->userRepository->addToUserGroup($userid, $groupid);
        }
        return true;

    }

    public function createGroup(string $group)
    {
        return $this->userRepository->createUserGroup($group);
    }

    public function findAllPartners(): array
    {
        return $this->userRepository->findAllPartners();
    }

    public function findGroupName($groupid): array
    {
        return $this->userRepository->getGroupName($groupid);
    }

    public function findGroupUsers($groupid): array
    {
        return $this->userRepository->getGroupUsers($groupid);
    }

}