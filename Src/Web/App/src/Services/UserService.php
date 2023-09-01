<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
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
        return $this->userRepository->findUserById($this->requestStack->getSession()->get("user_id"));
    }
}