<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IAuthenticationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/login", name: "signin", methods: ["GET"])]
    public function signin(): Response
    {
        return $this->render("authentication/signin.html");
    }

    #[Route("/signup", methods: ["GET"])]
    public function signupGET(): Response
    {
        return $this->render("authentication/signup.html");
    }

    #[Route("/signup", methods: ["POST"])]
    public function signupPOST(Request $request): Response
    {
        // if a user with given student email exists, send an email to that email 
        // address and show message saying that they should check their email
        return $this->render("authentication/signup.html");
    }

    #[Route("/signup/details", methods: ["GET"])]
    public function signupDetailsGET(): Response
    {
        return $this->render("authentication/signup_details.html");
    }

    #[Route("/signup/details", methods: ["POST"])]
    public function signupDetailsPOST(): Response|RedirectResponse
    {
        return new RedirectResponse("/");
    }

    #[Route("/register", name: "register")]
    public function register(): Response
    {
        return $this->render("authentication/register.html");
    }

    #[Route("/login", name: "login", methods: ["POST"])]
    public function login(Request $request): RedirectResponse
    {
        $req = $request->request;
        $email = $req->get("email", "");
        $passwordHash = $req->get("password", "");

        // validate form data
        // todo

        if ($this->authn->authenticate($email, $passwordHash)) {
            return new RedirectResponse("/home");
        }

        $request->getSession()->getFlashBag()->add("signin_error", "Invalid Email or Password");

        return new RedirectResponse("/");
    }

    #[Route("/logout", name: "logout")]
    public function logout(): RedirectResponse
    {
        $this->authn->logout();
        return new RedirectResponse("/");
    }
}