<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use Symfony\Component\HttpFoundation\RequestStack;

class UserService
{
    public function __construct(private RequestStack $requestStack)
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

        return new User("user@mail.com", "Ashen");
    }
}