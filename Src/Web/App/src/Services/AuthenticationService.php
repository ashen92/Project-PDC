<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationService implements IAuthenticationService
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function isAuthenticated(): bool
    {
        if ($this->requestStack->getSession()->has("is_authenticated"))
            return true;
        return false;
    }

    public function authenticate(string $email, string $passwordHash): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findUserByEmail($email);

        if (!$user || !password_verify($passwordHash, $user->getPasswordHash())) {
            return false;
        }

        $session = $this->requestStack->getSession();
        $session->set("is_authenticated", true);
        $session->set("user_id", $user->getId());
        $session->set("user_email", $user->getEmail());
        $session->set("user_first_name", $user->getFirstName());
        return true;
    }

    public function logout(): void
    {
        $this->requestStack->getSession()->invalidate();
    }
}