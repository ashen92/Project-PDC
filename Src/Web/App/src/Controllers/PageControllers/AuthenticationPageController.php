<?php
declare(strict_types=1);

namespace App\Controllers\PageControllers;

use App\Interfaces\IAuthenticationService;
use App\Interfaces\IAuthorizationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AuthenticationPageController extends PageControllerBase
{
    private IAuthenticationService $authn;

    public function __construct(
        IAuthorizationService $authz,
        Environment $twig,
        IAuthenticationService $authn
    ) {
        $this->authn = $authn;
        parent::__construct($authz, $twig);
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

}