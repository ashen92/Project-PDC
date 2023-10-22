<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationService implements IAuthenticationService
{
    public function __construct(
        private SessionInterface $session,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function authenticate(string $email, string $passwordHash): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findUserByEmail($email);

        if (!$user || !password_verify($passwordHash, $user->getPasswordHash())) {
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