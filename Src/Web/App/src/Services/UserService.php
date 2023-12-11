<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\Entities\User;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use App\Repositories\UserRepository;

class UserService implements IUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO)
    {
        $user = $this->userRepository->getUserById($createStudentDTO->id);

        $user->setFirstName($createStudentDTO->firstName);
        $user->setLastName($createStudentDTO->lastName);
        $user->setEmail($createStudentDTO->email);
        $user->setPasswordHash($this->passwordHasher->hashPassword($createStudentDTO->password));
        $user->setIsActive(true);
        $user->setActivationToken(null);
        $user->setActivationTokenExpiresAt(null);

        $this->userRepository->save($user);
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        return $this->userRepository->getUserRoles($userId);
    }

    public function hasRole(int $userId, string $role): bool
    {
        if ($role == "")
            return true;
        $roles = $this->userRepository->getUserRoles($userId);
        if (in_array($role, $roles))
            return true;
        return false;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function getUserByStudentEmail(string $email): ?User
    {
        return $this->userRepository->getUserByStudentEmail($email);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->userRepository->getUserByActivationToken($token);
    }

    public function saveUser(User $user): void
    {
        $this->userRepository->save($user);
    }
}