<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\CreateInternshipCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IRequirementService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    "ROLE_ADMIN",
    "ROLE_INTERNSHIP_PARTNER",
    "ROLE_INTERNSHIP_MANAGED_PARTNER",
    "ROLE_INTERNSHIP_STUDENT"
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

        if ($this->userService->hasRole($userId, "ROLE_ADMIN")) {
            return $this->render(
                "internship-program/home-admin.html",
                [
                    "section" => "home",
                    "internshipCycle" => $this->internshipCycleService->getLatestInternshipCycle()
                ]
            );
        }
        if ($this->userService->hasRole($userId, "ROLE_INTERNSHIP_PARTNER")) {
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
                "internshipCycle" => $this->internshipCycleService->getLatestInternshipCycle()
            ]
        );
    }

    #[Route("/users/create", methods: ["GET"], name: "user_create")]
    public function userCreate(Request $request): Response
    {
        return $this->render(
            "internship-program/create_user.html"
        );
    }

    #[Route("/users/create", methods: ["POST"])]
    public function userCreatePost(Request $request): RedirectResponse
    {
        $dto = new CreateUserDTO(
            $request->get("user-type"),
            $request->get("email"),
            $request->get("first-name"),
        );

        $this->internshipCycleService->createUserFor($request->getSession()->get("user_id"), $dto);

        return $this->redirect("/internship-program");
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
        $createInternshipCycleDTO = new CreateInternshipCycleDTO(
            $request->get("collection-start-date"),
            $request->get("collection-end-date"),
            $request->get("application-start-date"),
            $request->get("application-end-date"),
            (int) $request->get("partner-group"),
            (int) $request->get("student-group")
        );
        // validate DTO
        // todo

        $this->internshipCycleService->createInternshipCycle($createInternshipCycleDTO);
        return $this->redirect("/internship-program/cycle/details");
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
                    "section" => "requirements",
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