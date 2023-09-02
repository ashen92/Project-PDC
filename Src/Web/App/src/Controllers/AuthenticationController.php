<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IAuthenticationService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController
{
    public function __construct(
        private IAuthenticationService $authnService,
        private UserService $userService
    ) {

    }

    public function login(): RedirectResponse
    {
        // get form data and validate
        // todo

        if ($this->authnService->login("admin@mail.com", "12345")) {
            return new RedirectResponse("/home");
        }

        // set errors
        // todo

        return new RedirectResponse("/");
    }

    public function logout(): RedirectResponse
    {
        $this->authnService->logout();
        return new RedirectResponse("/");
    }

    public function signup(Request $request)
    {
        // todo
    }

    public function register(Request $request)
    {
        // todo
    }
}