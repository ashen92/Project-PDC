<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationService implements IAuthenticationService
{
    public function __construct(
        private SessionInterface $session,
        private IUserService $userService,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user || !$this->passwordHasher->verifyPassword($password, $user->getPasswordHash())) {
            return false;
        }

        $this->session->set("is_authenticated", true);
        $this->session->set("user_id", $user->getId());
        $this->session->set("user_email", $user->getEmail());
        $this->session->set("user_first_name", $user->getFirstName());
        return true;
    }

    public function logout(): void
    {
        $this->session->invalidate();
    }
}