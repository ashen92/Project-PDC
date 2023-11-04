<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IUserService
{
    public function getUserRoles(int $userId): array;
    public function invalidateUserCache(int $userId): void;
    public function hasRole(int $userId, string $role): bool;
}