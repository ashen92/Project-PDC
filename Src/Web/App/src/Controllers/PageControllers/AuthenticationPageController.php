<?php
declare(strict_types=1);

namespace App\PageControllers\Controllers;

use App\Interfaces\IAuthenticationService;
use App\Interfaces\IAuthorizationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function signin(Request $request): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/signin.html");
    }

    public function signup(Request $request): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/signup.html");
    }

    public function register(Request $request): Response|RedirectResponse
    {
        if ($this->authn->isAuthenticated()) {
            return $this->redirect("/home");
        }
        return $this->render("authentication/register.html");
    }

}