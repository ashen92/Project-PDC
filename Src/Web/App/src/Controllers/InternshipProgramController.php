<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IUserGroupService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
    private IUserService $userService;
    private IUserGroupService $userGroupService;

    public function __construct(
        Environment $twig,
        IUserService $userService,
        IUserGroupService $userGroupService
    ) {
        $this->userService = $userService;
        $this->userGroupService = $userGroupService;
        parent::__construct($twig);
    }

    #[Route(["", "/", "/home"], name: "home")]
    public function home(Request $request): Response
    {
        return $this->render("internship-program/home.html", ["section" => "home"]);
    }

    #[Route("/cycle/create", name: "cycle_create")]
    public function cycleCreate(Request $request): Response
    {
        return $this->render(
            "internship-program/cycle/create.html",
            [
                "section" => "home",
                "userGroups" => $this->userGroupService->getUserGroupsForInternshipProgram()
            ]
        );
    }

    #[Route("/cycle/details", name: "cycle_details")]
    public function cycleDetails(Request $request): Response
    {
        return $this->render("internship-program/cycle/details.html", ["section" => "home"]);
    }

    #[Route("/monitoring", methods: ["GET"])]
    public function monitoring(Request $request): Response
    {
        return $this->render("internship-program/monitoring/home.html", ["section" => "monitoring"]);
    }

    #[Route("/documents", name: "documents")]
    public function cycleDocuments(Request $request): Response
    {
        return $this->render("internship-program/documents.html", ["section" => "documents"]);
    }

    #[Route("/feedback", name: "feedback")]
    public function cycleFeedback(Request $request): Response
    {
        return $this->render("internship-program/feedback.html", ["section" => "feedback"]);
    }
}