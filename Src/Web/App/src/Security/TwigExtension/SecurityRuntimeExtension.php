<?php
declare(strict_types=1);

namespace App\Security\TwigExtension;

use App\Security\AuthorizationService;

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

    public function hasRole(string $role): bool
    {
        return $this->authorizationService->hasRole($role);
    }

    public function isAuthorized(string $policyName): bool
    {
        return $this->authorizationService->authorize($policyName);
    }
}