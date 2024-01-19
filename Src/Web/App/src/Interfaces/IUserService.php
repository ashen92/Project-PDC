<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Models\Partner;
use App\Models\Student;
use App\Models\User;
use App\Security\Role;

interface IUserService
{
    /**
     * @return int The ID of the created user
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createUser(CreateUserDTO $userDTO): int;

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO);

    /**
     * @return array<string>
     */
    public function getUserRoles(int $userId): array;

    public function hasRole(int $userId, Role $role): bool;
    public function getUserByEmail(string $email): ?User;
    public function getStudentByStudentEmail(string $email): ?Student;
    public function getUserByActivationToken(string $token): ?User;

    /**
     * @return array<Partner>
     */
    public function getManagedUsers(int $userId): array;

    public function generateActivationToken(User $user): string;

    public function updateUser(User $user): void;

    public function searchUsers(
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array;

    public function searchGroups(
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array;

    public function managePartner(int $managedBy, int $partnerId): bool;
}