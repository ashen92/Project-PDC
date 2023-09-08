<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Entities\User;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationService implements IAuthenticationService
{
    public function __construct(private UserRepository $userRepository, private RequestStack $requestStack)
    {

    }

    public function isAuthenticated(): bool
    {
        if ($this->requestStack->getSession()->get("is_authenticated"))
            return true;
        return false;
    }

    public function login(string $email, string $password): bool
    {
        // $user = $this->userRepository->findUserByEmail($email);

        // if (!$user || !password_verify($password, $user->getPasswordHash())) {
        //     return false;
        // }

        $session = $this->requestStack->getSession();
        $session->set("is_authenticated", true);
        // $session->set("user_email", $user->getEmail());
        // $session->set("user_first_name", $user->getFirstName());
        return true;
    }

    public function logout(): void
    {
        $this->requestStack->getSession()->invalidate();
    }
}