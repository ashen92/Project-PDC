<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IAuthenticationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController
{
    public function __construct(private IAuthenticationService $authService)
    {

    }

    public function login(Request $request): RedirectResponse
    {
        // get form data and validate
        // todo

        $user = $this->authService->authenticate("mail@mail.com", "12345");

        if ($user) {
            $session = $request->getSession();
            $session->set("is_authenticated", true);

            return new RedirectResponse("/home");
        }

        // set errors
        // todo

        return new RedirectResponse("/");
    }

    public function logout(Request $request)
    {
        // todo
    }

    public function signup(Request $request)
    {
        // todo
    }
}