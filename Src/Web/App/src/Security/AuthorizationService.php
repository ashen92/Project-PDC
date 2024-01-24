<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class AuthorizationService
{
    public function __construct(
        private AuthorizationRepository $authzRepo,
        private SessionInterface $session,
    ) {

    }

    public function hasRole(Role $role): bool
    {
        $userId = (int) $this->session->get('user_id');
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

    public function hasPermission(string $resource, string $action): bool
    {
        $userId = (int) $this->session->get('user_id');
        return $this->authzRepo->hasPermission($userId, $resource, $action);
    }
}