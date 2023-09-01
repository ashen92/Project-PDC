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

        $user = $this->authService->login("mail@mail.com", "12345");

        if ($user) {
            $session = $request->getSession();
            $session->set("is_authenticated", true);
            $session->set("user_id", "id");
            $session->set("user_email", $user->getEmail());
            $session->set("user_first_name", $user->getFirstName());

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