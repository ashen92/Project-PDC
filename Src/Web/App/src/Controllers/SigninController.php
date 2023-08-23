<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SigninController
{
    public function __construct(private Environment $twig)
    {
    }

    public function index(Request $request): Response|RedirectResponse
    {
        if ($request->getSession()->get("is_authenticated")) {
            return new RedirectResponse("/home");
        }
        return new Response($this->twig->render("signin.html"));
    }
}