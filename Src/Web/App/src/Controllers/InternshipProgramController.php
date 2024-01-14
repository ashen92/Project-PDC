<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\CreateCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IRequirementService;
use App\Interfaces\IUserService;
use App\Security\Role;
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
#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private IUserService $userService,
        private IInternshipCycleService $internshipCycleService,
        private IRequirementService $requirementService,
    ) {
        parent::__construct($twig);
    }

    #[Route(["", "/", "/home"], name: "home")]
    public function home(Request $request): Response
    {
        $userId = $request->getSession()->get("user_id");

        if ($this->userService->hasRole($userId, Role::InternshipProgram_Admin)) {
            return $this->render(
                "internship-program/home-admin.html",
                [
                    "section" => "home",
                    "internshipCycle" => $this->internshipCycleService->getLatestCycle()
                ]
            );
        }
        if ($this->userService->hasRole($userId, Role::InternshipProgram_Partner_Admin)) {
            return $this->render(
                "internship-program/home-partner.html",
                [
                    "section" => "home",
                    "users" => $this->userService->getManagedUsers($userId),
                ]
            );
        }
        return $this->render(
            "internship-program/home.html",
            [
                "section" => "home",
                "internshipCycle" => $this->internshipCycleService->getLatestCycle()
            ]
        );
    }

    #[Route("/users/create", methods: ["GET"], name: "user_create")]
    public function userCreate(Request $request): Response
    {
        return $this->render(
            "internship-program/create_user.html",
            ["section" => "home"]
        );
    }

    #[Route("/users/create", methods: ["POST"])]
    public function userCreatePost(Request $request): Response|RedirectResponse
    {
        $dto = new CreateUserDTO(
            $request->get("user-type"),
            $request->get("email"),
            $request->get("first-name"),
        );

        try {
            $this->internshipCycleService->createManagedUser($request->getSession()->get("user_id"), $dto);
        } catch (UserExistsException $e) {

            // TODO: Set error message

            return $this->render(
                "internship-program/create_user.html",
                ["section" => "home"]
            );
        }

        return $this->redirect("/internship-program/users/create");
    }

    #[Route("/cycle/create", methods: ["GET"])]
    public function cycleCreateGET(Request $request): Response
    {
        return $this->render(
            "internship-program/cycle/create.html",
            [
                "section" => "home",
                "eligiblePartnerGroups" => $this->internshipCycleService
                    ->getEligiblePartnerGroupsForInternshipCycle(),
                "eligibleStudentGroups" => $this->internshipCycleService
                    ->getEligibleStudentGroupsForInternshipCycle()
            ]
        );
    }

    #[Route("/cycle/create", methods: ["POST"])]
    public function cycleCreatePOST(Request $request): RedirectResponse
    {
        $createInternshipCycleDTO = new CreateCycleDTO(
            $request->get("collection-start-date"),
            $request->get("collection-end-date"),
            $request->get("application-start-date"),
            $request->get("application-end-date"),
            (int) $request->get("partner-group"),
            (int) $request->get("student-group")
        );
        // TODO: validate DTO

        // TODO: handle exceptions
        $this->internshipCycleService->createCycle($createInternshipCycleDTO);
        $request->getSession()->remove("latest_internship_cycle_id");

        return $this->redirect("/internship-program");
    }

    #[Route("/cycle/end")]
    public function cycleEnd(Request $request): RedirectResponse
    {
        $this->internshipCycleService->endInternshipCycle();
        return $this->redirect("/internship-program");
    }

    #[Route("/monitoring", methods: ["GET"])]
    public function monitoring(Request $request): Response
    {
        return $this->render(
            "internship-program/monitoring/home.html",
            [
                "section" => "monitoring",
                "requirements" => $this->requirementService->getRequirements()
            ]
        );
    }

    #[Route("/monitoring/students", methods: ["GET"])]
    public function monitoringStudentUsers(Request $request): Response
    {
        return $this->render(
            "internship-program/monitoring/student-users.html",
            [
                "section" => "monitoring",
                "users" => $this->internshipCycleService->getStudentUsers()
            ]
        );
    }

    #[Route("/monitoring/submissions", methods: ["GET"])]
    public function requirementSubmissions(Request $request): Response|RedirectResponse
    {
        $id = (int) $request->get("r");

        $requirement = $this->requirementService->getRequirement($id);
        if ($requirement) {
            return $this->render(
                "internship-program/monitoring/submissions.html",
                [
                    "section" => "monitoring",
                    "requirement" => $requirement,
                    "userRequirements" => $this->requirementService->getUserRequirements(
                        requirementId: $id,
                        status: "completed"
                    )
                ]
            );
        }
        return $this->redirect("/internship-program/monitoring");
    }
}