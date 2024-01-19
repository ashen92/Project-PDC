<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Interfaces\IEmailService;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use App\Models\Student;
use App\Models\UserInviteEmail;
use App\Repositories\UserRepository;
use App\Security\Role;

class UserService implements IUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private IPasswordHasher $passwordHasher,
        private IEmailService $emailService
    ) {
    }

    #[\Override] public function createUser(CreateUserDTO $userDTO): int
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

    #[\Override] public function createStudentUser(CreateStudentUserDTO $createStudentDTO): void
    {
        $user = $this->userRepository->findUser($createStudentDTO->id);

        $user->setFirstName($createStudentDTO->firstName);
        $user->setLastName($createStudentDTO->lastName);
        $user->setEmail($createStudentDTO->email);
        $user->setPasswordHash($this->passwordHasher->hashPassword($createStudentDTO->password));
        $user->setIsActive(true);
        $user->resetActivationToken();

        $this->userRepository->updateUser($user);
    }

    #[\Override] public function getUserRoles(int $userId): array
    {
        return $this->userRepository->findUserRoles($userId);
    }

    #[\Override] public function hasRole(int $userId, Role $role): bool
    {
        if ($role == "")
            return true;
        $roles = $this->userRepository->findUserRoles($userId);
        if (in_array($role->value, $roles))
            return true;
        return false;
    }

    #[\Override] public function getUserByEmail(string $email): ?\App\Models\User
    {
        return $this->userRepository->findByEmail($email);
    }

    #[\Override] public function getStudentByStudentEmail(string $email): ?Student
    {
        return $this->userRepository->findStudentByStudentEmail($email);
    }

    #[\Override] public function getUserByActivationToken(string $token): ?\App\Models\User
    {
        return $this->userRepository->findByActivationToken($token);
    }

    #[\Override] public function generateActivationToken(\App\Models\User $user): string
    {
        $token = $user->generateActivationToken();
        $this->userRepository->updateUser($user);
        return $token;
    }

    #[\Override] public function getManagedUsers(int $userId): array
    {
        return $this->userRepository->findManagedUsers($userId);
    }

    #[\Override] public function updateUser(\App\Models\User $user): void
    {
        $this->userRepository->updateUser($user);
    }

    #[\Override] public function searchUsers(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchUsers($numberOfResults, $offsetBy);
    }

    #[\Override] public function searchGroups(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchGroups($numberOfResults, $offsetBy);
    }

    #[\Override] public function managePartner(int $managedBy, int $partnerId): bool
    {
        return $this->userRepository->managePartner($managedBy, $partnerId);
    }
}