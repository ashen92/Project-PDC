<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\StudentUserViewDTO;
use App\DTOs\UserActivationTokenDTO;
use App\DTOs\UserViewDTO;

interface IUserService {
    public function createStudentUser(CreateStudentUserDTO $createStudentDTO);
    public function getUserRoles(int $userId): array;
    public function hasRole(int $userId, string $role): bool;
    public function getUserByEmail(string $email): ?UserViewDTO;
    public function getUserByStudentEmail(string $email): ?StudentUserViewDTO;
    public function getUserByActivationToken(string $token): ?UserViewDTO;
    public function saveActivationToken(UserActivationTokenDTO $userActivationTokenDTO): void;
}