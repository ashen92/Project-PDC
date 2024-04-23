<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\UpdateStudentUserDTO;
use App\Interfaces\IEmailService;
use App\Models\SignupEmail;
use App\Security\AuthorizationService;
use App\Services\AuthenticationService;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AuthenticationController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly AuthenticationService $authn,
        private readonly IEmailService $emailService
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route('/login', methods: ['GET'])]
    public function loginGET(Request $request): Response
    {
        return $this->render(
            'authentication/signin.html',
            [
                'redirect' => $request->query->get('redirect', '/')
            ]
        );
    }

    #[Route('/login', methods: ['POST'])]
    public function loginPOST(Request $request): RedirectResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');

        // TODO: Validate email and password

        $user = $this->authn->login($email, $password);

        if ($user) {
            $session = $request->getSession();

            $session->set('is_authenticated', true);
            $session->set('user_id', $user->getId());
            $session->set('user_email', $user->getEmail());
            $session->set('user_first_name', $user->getFirstName() ?? 'Not set');

            return new RedirectResponse($request->query->get('redirect', '/'));
        }

        $request->getSession()->getFlashBag()->add('signin_error', 'Invalid Email or Password');

        return new RedirectResponse('/');
    }

    #[Route('/signup', methods: ['GET'])]
    public function signupGET(): Response
    {
        return $this->render('authentication/signup.html');
    }

    #[Route('/signup', methods: ['POST'])]
    public function signupPOST(Request $request): Response|RedirectResponse
    {
        // TODO: Handle errors

        $email = $request->get('student-email');
        // TODO: Validate email

        if ($email) {
            $email = $email . '@stu.ucsc.cmb.ac.lk';
            $user = $this->authn->getStudentByStudentEmail($email);
            if ($user) {
                if (!$user->isActive()) {
                    $token = $this->authn->generateActivationToken($user);
                    $email = new SignupEmail($email, $user->getFullName(), $token);
                    $this->emailService->sendEmail($email);

                    return $this->redirect('/login');
                }
            }
        }
        return $this->render('authentication/signup.html');
    }

    #[Route('/signup/continue', methods: ['GET'])]
    public function signupActivateGET(Request $request): Response
    {
        // TODO: Handle errors

        $token = $request->query->get('token');
        // TODO: Validate token

        if ($token) {
            $user = $this->authn->getUserByActivationToken($token);
            if ($user) {
                if ($user->getActivationTokenExpiresAt() > new DateTime('now')) {
                    return $this->render(
                        'authentication/activate.html',
                        ['token' => $token]
                    );
                }

                // token is expired. Show error message

                $this->authn->resetActivationToken($user);
            }
        }
        return $this->redirect('/login');
    }

    #[Route('/signup/continue', methods: ['POST'])]
    public function signupActivatePOST(Request $request): Response
    {
        // TODO: Handle errors

        $token = $request->get('token');
        // TODO: Validate token

        if ($token) {
            $user = $this->authn->getUserByActivationToken($token);
            if ($user) {
                if ($user->getActivationTokenExpiresAt() > new DateTime('now')) {

                    $dto = new UpdateStudentUserDTO(
                        $request->get('first-name'),
                        $request->get('last-name'),
                        $request->get('email'),
                        $request->get('password'),
                        $request->get('confirm-password'),
                    );

                    // TODO: Validate

                    $this->authn->updateStudentUser($user, $dto);

                    return $this->redirect('/login');
                }

                // token is expired. Handle error
                // TODO
            }
        }
        return $this->redirect('/login');
    }

    #[Route('/register')]
    public function register(): Response
    {
        return $this->render('authentication/register.html');
    }

    #[Route('/logout')]
    public function logout(Request $request): RedirectResponse
    {
        $request->getSession()->invalidate();
        return new RedirectResponse('/');
    }
}