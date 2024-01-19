<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateStudentUserDTO;
use App\Interfaces\IAuthenticationService;
use App\Interfaces\IEmailService;
use App\Interfaces\IUserService;
use App\Models\SignupEmail;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AuthenticationController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private IAuthenticationService $authn,
        private IUserService $userService,
        private IEmailService $emailService
    ) {
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
        // Handle errors
        // todo
        $email = $request->request->get("student-email", null);
        // Validate email
        // todo
        if ($email) {
            $email = "{$email}@stu.ucsc.cmb.ac.lk";
            $user = $this->userService->getUserByStudentEmail($email);
            if ($user) {
                if (!$user->isActive()) {

                    $token = $user->generateActivationToken();
                    $this->userService->saveUser($user);

                    $email = new SignupEmail($email, $user->getFullName(), $token);
                    $this->emailService->sendEmail($email);

                    return $this->redirect("/login");
                }
            }
        }
        return $this->render("authentication/signup.html");
    }

    #[Route("/signup/continue", methods: ["GET"])]
    public function signupActivateGET(Request $request): Response
    {
        // Handle errors
        // todo
        $token = $request->query->get("token", null);
        // Validate token
        // todo
        if ($token) {
            $user = $this->userService->getUserByActivationToken($token);
            if ($user) {
                if ($user->getActivationTokenExpiresAt() > new DateTime("now")) {
                    return $this->render(
                        "authentication/activate.html",
                        ["token" => $token]
                    );
                }

                // token is expired. Show error message

                $user->resetActivationToken();
                $this->userService->updateUser($user);
            }
        }
        return $this->redirect("/login");
    }

    #[Route("/signup/continue", methods: ["POST"])]
    public function signupActivatePOST(Request $request): Response
    {
        // Handle errors
        // todo
        $token = $request->get("token", null);
        // Validate token
        // todo
        if ($token) {
            $user = $this->userService->getUserByActivationToken($token);
            if ($user) {
                if ($user->getActivationTokenExpiresAt() > new DateTime("now")) {

                    $createStudentDTO = new CreateStudentUserDTO(
                        $user->getId(),
                        $request->get("first-name", null),
                        $request->get("last-name", null),
                        $request->get("email", null),
                        $request->get("password", null),
                        $request->get("confirm-password", null),
                    );

                    // Validate createStudentDTO
                    // todo

                    $this->userService->createStudentUser($createStudentDTO);

                    return $this->redirect("/login");
                }

                // token is expired. Handle error
            }
        }
        return $this->redirect("/login");
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
        $password = $req->get("password", "");

        // validate form data
        // todo

        $user = $this->authn->login($email, $password);

        if ($user) {
            $session = $request->getSession();

            $session->set("is_authenticated", true);
            $session->set("user_id", $user->getId());
            $session->set("user_email", $user->getEmail());
            $session->set("user_first_name", $user->getFirstName() ?? "Not set");

            return new RedirectResponse("/home");
        }

        $request->getSession()->getFlashBag()->add("signin_error", "Invalid Email or Password");

        return new RedirectResponse("/");
    }

    #[Route("/logout", name: "logout")]
    public function logout(Request $request): RedirectResponse
    {
        $request->getSession()->invalidate();
        return new RedirectResponse("/");
    }
}