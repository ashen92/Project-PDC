<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthorizationService;

class AuthorizationService implements IAuthorizationService
{
    public function __construct(private UserService $userService)
    {
    }

    public function isAuthorized($requiredRole)
    {
        $currentUser = $this->userService->getCurrentUser();
        $userRoles = $currentUser->getRoles(); // Assume getRoles returns an array of roles

        return in_array($requiredRole, $userRoles);
    }

    public function getUserRoles(): array
    {
        return $this->userService->getCurrentUser()->getRoles();
    }
}