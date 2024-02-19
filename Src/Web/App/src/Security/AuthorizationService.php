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

    public function hasPermission(string $resource, string $action): bool
    {
        $userId = (int) $this->session->get('user_id');
        return $this->authzRepo->hasPermission($userId, $resource, $action);
    }
}