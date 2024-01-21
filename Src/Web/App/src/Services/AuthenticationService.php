<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IPasswordHasher;

class AuthenticationService
{
    public function __construct(
        private UserService $userService,
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