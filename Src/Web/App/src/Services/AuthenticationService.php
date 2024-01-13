<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;

class AuthenticationService implements IAuthenticationService
{
    public function __construct(
        private IUserService $userService,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function login(string $email, string $password): ?\App\Models\User
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user || !$this->passwordHasher->verifyPassword($password, $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }
}