<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Interfaces\IEmailService;
use App\Interfaces\IPasswordHasher;
use App\Models\Partner;
use App\Models\Student;
use App\Models\User;
use App\Models\UserInviteEmail;
use App\Repositories\UserRepository;
use App\Security\Role;

class UserService
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

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO): void
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

    /**
     * @return array<string>
     */
    public function getUserRoles(int $userId): array
    {
        return $this->userRepository->findUserRoles($userId);
    }

    public function hasRole(int $userId, Role $role): bool
    {
        if ($role == "")
            return true;
        $roles = $this->userRepository->findUserRoles($userId);
        if (in_array($role->value, $roles))
            return true;
        return false;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getStudentByStudentEmail(string $email): ?Student
    {
        return $this->userRepository->findStudentByStudentEmail($email);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->userRepository->findByActivationToken($token);
    }

    public function generateActivationToken(User $user): string
    {
        $token = $user->generateActivationToken();
        $this->userRepository->updateUser($user);
        return $token;
    }

    /**
     * @return array<Partner>
     */
    public function getManagedUsers(int $userId): array
    {
        return $this->userRepository->findManagedUsers($userId);
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->updateUser($user);
    }

    public function searchUsers(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchUsers($numberOfResults, $offsetBy);
    }

    public function searchGroups(?int $numberOfResults, ?int $offsetBy): array
    {
        return $this->userRepository->searchGroups($numberOfResults, $offsetBy);
    }

    public function managePartner(int $managedBy, int $partnerId): bool
    {
        return $this->userRepository->managePartner($managedBy, $partnerId);
    }
}