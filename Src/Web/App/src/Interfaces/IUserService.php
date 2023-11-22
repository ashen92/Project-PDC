<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\User;

interface IUserService
{
    public function getUserRoles(int $userId): array;
    public function invalidateUserCache(int $userId): void;
    public function hasRole(int $userId, string $role): bool;
    public function getUserByStudentEmail(string $email): User;
    public function saveUser(User $user): void;
}