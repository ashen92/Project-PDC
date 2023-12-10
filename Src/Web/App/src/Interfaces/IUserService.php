<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\UserViewDTO;
use App\Entities\User;

interface IUserService
{
    public function createStudentUser(CreateStudentUserDTO $createStudentDTO);
    public function getUserRoles(int $userId): array;
    public function hasRole(int $userId, string $role): bool;
    public function getUserByEmail(string $email): ?UserViewDTO;
    public function getUserByStudentEmail(string $email): ?User;
    public function getUserByActivationToken(string $token): ?User;
    public function saveUser(User $user): void;
}