<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\UserActivationTokenDTO;
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

class AuthenticationController extends PageControllerBase {
    private IAuthenticationService $authn;
    private IUserService $userService;
    private IEmailService $emailService;

    public function __construct(
        Environment $twig,
        IAuthenticationService $authn,
        IUserService $userService,
        IEmailService $emailService
    ) {
        $this->authn = $authn;
        $this->userService = $userService;
        $this->emailService = $emailService;
        parent::__construct($twig);
    }

    #[Route("/login", name: "signin", methods: ["GET"])]
    public function signin(): Response {
        return $this->render("authentication/signin.html");
    }

    #[Route("/signup", methods: ["GET"])]
    public function signupGET(): Response {
        return $this->render("authentication/signup.html");
    }

    #[Route("/signup", methods: ["POST"])]
    public function signupPOST(Request $request): Response {
        // Handle errors
        // todo
        $email = $request->request->get("student-email", null);
        // Validate email
        // todo
        if($email) {
            $email = "{$email}@stu.ucsc.cmb.ac.lk";
            $user = $this->userService->getUserByStudentEmail($email);
            if($user) {
                if($user->isActive == false) {

                    $token = bin2hex(random_bytes(32));
                    $expirationTime = new DateTime("+1 day");

                    $userUpdateDTO = new UserActivationTokenDTO(
                        id: $user->id,
                        activationToken: $token,
                        activationTokenExpiresAt: $expirationTime
                    );

                    $this->userService->saveActivationToken($userUpdateDTO);

                    $email = new SignupEmail($email, $user->fullName, $token);

                    $this->emailService->sendEmail($email);

                    return $this->redirect("/login");
                }
            }
        }
        return $this->render("authentication/signup.html");
    }

    #[Route("/signup/activate", methods: ["GET"])]
    public function signupActivateGET(Request $request): Response {
        // Handle errors
        // todo
        $token = $request->query->get("token", null);
        // Validate token
        // todo
        if($token) {
            $user = $this->userService->getUserByActivationToken($token);
            if($user) {
                if($user->activationTokenExpiresAt > new DateTime("now")) {
                    return $this->render(
                        "authentication/activate.html",
                        ["token" => $token]
                    );
                }

                $dto = new UserActivationTokenDTO($user->id);
                $this->userService->saveActivationToken($dto);
            }
        }
        return $this->redirect("/login");
    }

    #[Route("/signup/activate", methods: ["POST"])]
    public function signupActivatePOST(Request $request): Response {
        // Handle errors
        // todo
        $token = $request->get("token", null);
        // Validate token
        // todo
        if($token) {
            $user = $this->userService->getUserByActivationToken($token);
            if($user) {
                if($user->activationTokenExpiresAt > new DateTime("now")) {

                    $createStudentDTO = new CreateStudentUserDTO(
                        $user->id,
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
            }
        }
        return $this->redirect("/login");
    }

    #[Route("/register", name: "register")]
    public function register(): Response {
        return $this->render("authentication/register.html");
    }

    #[Route("/login", name: "login", methods: ["POST"])]
    public function login(Request $request): RedirectResponse {
        $req = $request->request;
        $email = $req->get("email", "");
        $password = $req->get("password", "");

        // validate form data
        // todo

        if($this->authn->authenticate($email, $password)) {
            return new RedirectResponse("/home");
        }

        $request->getSession()->getFlashBag()->add("signin_error", "Invalid Email or Password");

        return new RedirectResponse("/");
    }

    #[Route("/logout", name: "logout")]
    public function logout(): RedirectResponse {
        $this->authn->logout();
        return new RedirectResponse("/");
    }
}