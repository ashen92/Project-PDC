<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\User;
use App\DTOs\CreateStudentDTO;

interface IUserService
{
    public function createUserStudent(User $user, CreateStudentDTO $createStudentDTO);
    public function getUserRoles(int $userId): array;
    public function invalidateUserCache(int $userId): void;
    public function hasRole(int $userId, string $role): bool;
    public function getUserByEmail(string $email): User;
    public function getUserByStudentEmail(string $email): User;
    public function getUserByActivationToken(string $token): User;
    public function saveUser(User $user): void;
}