<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Models\User;
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

    public function login(string $email, string $password): User|null
    {
        // authenticate against database. use UserRepository
        // todo

        $user = $this->userRepository->findUserByEmail($email);

        // if (!$user || !password_verify($password, $user->getPasswordHash())) {
        //     return null;
        // }

        return $user;
    }

    public function logout(): void
    {

    }
}