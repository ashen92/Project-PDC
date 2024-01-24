<?php
declare(strict_types=1);

namespace App\TwigExtension;

use App\Security\AuthorizationService;
use App\Security\Role;

class SecurityRuntimeExtension extends \Twig\Extension\AbstractExtension
{
    public function __construct(
        protected readonly AuthorizationService $authorizationService,
    ) {
    }

    public function hasPermission(string $resource, string $action): bool
    {
        return $this->authorizationService->hasPermission($resource, $action);
    }

    public function hasRole(Role $role): bool
    {
        return $this->authorizationService->hasRole($role);
    }
}