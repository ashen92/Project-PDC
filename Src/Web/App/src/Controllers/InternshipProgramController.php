<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\CreateInternshipCycleDTO;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IRequirementService;
use App\Interfaces\IUserGroupService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    "ROLE_ADMIN",
    "ROLE_INTERNSHIP_PARTNER",
    "ROLE_INTERNSHIP_STUDENT"
])]
#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private IUserService $userService,
        private IUserGroupService $userGroupService,
        private IInternshipCycleService $internshipCycleService,
        private IRequirementService $requirementService
    ) {
        parent::__construct($twig);
    }

    #[Route(["", "/", "/home"], name: "home")]
    public function home(Request $request): Response
    {
        return $this->render(
            "internship-program/home.html",
            [
                "section" => "home",
                "internshipCycle" => $this->internshipCycleService->getLatestInternshipCycle()
            ]
        );
    }

    #[Route("/cycle/create", methods: ["GET"])]
    public function cycleCreateGET(Request $request): Response
    {
        return $this->render(
            "internship-program/cycle/create.html",
            [
                "section" => "home",
                "userGroups" => $this->userGroupService->getUserGroupsForInternshipProgram()
            ]
        );
    }

    #[Route("/cycle/create", methods: ["POST"])]
    public function cycleCreatePost(Request $request): RedirectResponse
    {
        $createInternshipCycleDTO = new CreateInternshipCycleDTO(
            $request->get("collection-start-date"),
            $request->get("collection-end-date"),
            $request->get("application-start-date"),
            $request->get("application-end-date"),
            $request->get("partner-group"),
            $request->get("student-group")
        );
        // validate DTO
        // todo

        $this->internshipCycleService->createInternshipCycle($createInternshipCycleDTO);
        return $this->redirect("/internship-program/cycle/details");
    }

    #[Route("/cycle/details")]
    public function cycleDetails(Request $request): Response
    {
        return $this->render(
            "internship-program/cycle/details.html",
            [
                "section" => "home",
                "internshipCycle" => $this->internshipCycleService->getLatestInternshipCycle()
            ]);
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

    #[Route("/documents")]
    public function cycleDocuments(Request $request): Response
    {
        return $this->render("internship-program/documents.html", ["section" => "documents"]);
    }

    #[Route("/feedback")]
    public function cycleFeedback(Request $request): Response
    {
        return $this->render("internship-program/feedback.html", ["section" => "feedback"]);
    }
}