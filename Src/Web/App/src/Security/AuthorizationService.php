<?php
declare(strict_types=1);

namespace App\Security;

readonly class AuthorizationService
{
    public function __construct(
        private AuthorizationRepository $authzRepo,
    ) {

    }

    public function hasRole(int $userId, Role $role): bool
    {
        return $this->authzRepo->hasRole($userId, $role);
    }

    /**
     * @return array<string>
     */
    public function getUserRolesAsStrings(int $userId): array
    {
        $roles = $this->authzRepo->findUserRoles($userId);
        return array_map(fn($role) => $role?->value, $roles);
    }

    public function hasPermission(string $permission): bool
    {
        echo "Checking permission: $permission\n";
        return false;
    }
}