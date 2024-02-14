<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredPolicy;
use App\Attributes\RequiredRole;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Models\InternshipCycle;
use App\Security\Identity;
use App\Security\Role;
use App\Services\InternshipProgramService;
use App\Services\RequirementService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    Role::InternshipProgram_Admin,
    Role::InternshipProgram_Partner_Admin,
    Role::InternshipProgram_Partner,
    Role::InternshipProgram_Student,
])]
#[Route('/internship-program')]
class InternshipProgramController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private readonly UserService $userService,
        private readonly InternshipProgramService $internshipCycleService,
        private readonly RequirementService $requirementService,
    ) {
        parent::__construct($twig);
    }

    #[Route([''])]
    public function home(Request $request, Identity $identity, ?InternshipCycle $cycle): Response
    {
        $userId = $request->getSession()->get('user_id');

        if ($identity->hasRole(Role::InternshipProgram_Admin)) {
            return $this->render(
                'internship-program/home-admin.html',
                [
                    'section' => 'home',
                    'internshipCycle' => $cycle
                ]
            );
        }
        if ($identity->hasRole(Role::InternshipProgram_Partner_Admin)) {
            return $this->render(
                'internship-program/home-partner.html',
                [
                    'section' => 'home',
                    'users' => $this->userService->getManagedUsers($userId),
                ]
            );
        }
        return $this->render(
            'internship-program/home.html',
            [
                'section' => 'home',
                'internshipCycle' => $cycle
            ]
        );
    }

    #[Route('/users/create', methods: ['GET'])]
    public function userCreate(): Response
    {
        return $this->render(
            'internship-program/create_user.html',
            ['section' => 'home']
        );
    }

    #[Route('/users/create', methods: ['POST'])]
    public function userCreatePost(Request $request): Response|RedirectResponse
    {
        $dto = new CreateUserDTO(
            $request->get('user-type'),
            $request->get('email'),
            $request->get('first-name'),
        );

        try {
            $this->internshipCycleService->createManagedUser($request->getSession()->get('user_id'), $dto);
        } catch (UserExistsException) {

            // TODO: Set error message

            return $this->render(
                'internship-program/create_user.html',
                ['section' => 'home']
            );
        }

        return $this->redirect('/internship-program/users/create');
    }

    #[RequiredRole(Role::InternshipProgram_Admin)]
    #[RequiredPolicy(InternshipCycle\State::Ended)]
    #[Route('/cycle/create', methods: ['GET'])]
    public function cycleCreateGET(): Response
    {
        return $this->render(
            'internship-program/cycle/create.html',
            [
                'section' => 'home',
                'eligiblePartnerGroups' => $this->internshipCycleService
                    ->getEligiblePartnerGroupsForInternshipCycle(),
                'eligibleStudentGroups' => $this->internshipCycleService
                    ->getEligibleStudentGroupsForInternshipCycle()
            ]
        );
    }

    #[RequiredRole(Role::InternshipProgram_Admin)]
    #[RequiredPolicy(InternshipCycle\State::Ended)]
    #[Route('/cycle/create', methods: ['POST'])]
    public function cycleCreatePOST(Request $request): RedirectResponse
    {
        $partnerGroup = (int) $request->get('partner-group');
        $studentGroup = (int) $request->get('student-group');

        // TODO: Validate

        // TODO: handle exceptions
        $this->internshipCycleService->createCycle($partnerGroup, $studentGroup);

        return $this->redirect('/internship-program');
    }

    #[RequiredRole(Role::InternshipProgram_Admin)]
    #[RequiredPolicy(InternshipCycle\State::Active)]
    #[Route('/cycle/end')]
    public function cycleEnd(): RedirectResponse
    {
        $this->internshipCycleService->endInternshipCycle();
        return $this->redirect('/internship-program');
    }

    #[RequiredRole(Role::InternshipProgram_Student)]
    #[Route('/profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        $user = $this->userService->getUser($userId);

        return $this->render(
            'internship-program/profile/home.html',
            [
                'section' => 'profile',
                'user' => $user,
            ]
        );
    }
}