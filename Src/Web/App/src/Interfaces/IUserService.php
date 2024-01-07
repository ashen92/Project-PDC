<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\Partner;
use App\Entities\Student;
use App\Entities\User;
use App\Exceptions\UserExistsException;

interface IUserService
{
    /**
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createUser(CreateUserDTO $userDTO): User|Student|Partner;

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO);

    /**
     * @return array<string>
     */
    public function getUserRoles(int $userId): array;

    public function hasRole(int $userId, string $role): bool;
    public function getUserByEmail(string $email): ?User;
    public function getUserByStudentEmail(string $email): ?User;
    public function getUserByActivationToken(string $token): ?User;

    /**
     * @return array<Partner>
     */
    public function getManagedUsers(int $userId): array;

    public function saveUser(User $user): void;
}