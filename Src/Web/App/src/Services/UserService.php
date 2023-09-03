<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use App\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class UserService
{
    public function __construct(private RequestStack $requestStack, private UserRepository $userRepository)
    {
    }

    public function registerUser($username, $password)
    {
        // $encryptedPassword = "";
        // // Perform validations, encrypt password, etc.
        // $user = new User($username, $encryptedPassword);

        // // Save to database
        // $this->userRepository->saveUser($user);
    }

    public function getCurrentUser()
    {
        // Fetch the current user, possibly from the session
        // Can add more logic here, e.g., caching, transformations, etc.

        $session = $this->requestStack->getSession();
        $email = $session->get("user_email");

        if ($email == "admin@mail.com") {
            return new User("admin@mail.com", "Ashen", "12345", ["admin"]);
        }
        if ($email == "pdc@mail.com") {
            return new User("pdc@mail.com", "Ashen", "12345", ["admin"]);
        }
        if ($email == "partner@mail.com") {
            return new User("partner@mail.com", "Ashen", "12345", ["partner"]);
        }
        return new User("user@mail.com", "Ashen", "12345", ["user"]);
    }
}