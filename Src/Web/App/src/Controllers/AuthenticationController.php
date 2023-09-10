<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IAuthenticationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AuthenticationController extends PageControllerBase
{
    private IAuthenticationService $authn;

    public function __construct(
        Environment $twig,
        IAuthenticationService $authn
    ) {
        $this->authn = $authn;
        parent::__construct($twig);
    }

    protected function getSectionName(): string
    {
        return "";
    }

    protected function getSectionURL(): string
    {
        return "";
    }

    #[Route("/", name: "signin")]
    public function signin(): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/signin.html");
    }

    #[Route("/signup", name: "signup")]
    public function signup(): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/signup.html");
    }

    #[Route("/register", name: "register")]
    public function register(): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/register.html");
    }

    #[Route("/login", name: "login", methods: ["POST"])]
    public function login(): RedirectResponse
    {
        // get form data and validate
        // todo

        if ($this->authn->login("6@mail.com", "12345")) {
            return new RedirectResponse("/home");
        }

        // set errors
        // todo

        return new RedirectResponse("/");
    }

    #[Route("/logout", name: "logout")]
    public function logout(): RedirectResponse
    {
        $this->authn->logout();
        return new RedirectResponse("/");
    }
}