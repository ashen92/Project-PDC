<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\User;

interface IUserService
{
    public function createUser(CreateUserDTO $user): void;
    public function createStudentUser(CreateStudentUserDTO $createStudentDTO);
    public function getUserRoles(int $userId): array;
    public function hasRole(int $userId, string $role): bool;
    public function getUserByEmail(string $email): ?User;
    public function getUserByStudentEmail(string $email): ?User;
    public function getUserByActivationToken(string $token): ?User;
    public function saveUser(User $user): void;
}