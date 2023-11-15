<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\RequirementDTO;
use App\Interfaces\IInternshipService;
use App\Interfaces\IRequirementService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/internship-program/requirements")]
class RequirementController extends PageControllerBase
{
    private IInternshipService $internshipService;
    private IUserService $userService;

    private IRequirementService $requirementService;

    public function __construct(
        Environment $twig,
        IUserService $userService,
        IRequirementService $requirementService
    ) {
        $this->userService = $userService;
        $this->requirementService = $requirementService;
        parent::__construct($twig);
    }

    #[Route(["", "/"], methods: ["GET"])]
    public function requirements(Request $request): Response
    {
        $userId = $request->getSession()->get("user_id");
        if ($this->userService->hasRole($userId, "ROLE_ADMIN")) {
            return $this->render(
                "internship-program/requirements/home.html",
                [
                    "section" => "requirements",
                    "requirements" => $this->requirementService->getRequirements()
                ]
            );
        }

        return $this->render(
            "internship-program/requirements/home.html",
            [
                "section" => "requirements",
                "requirements" => $this->requirementService->getUserRequirements($userId)
            ]
        );
    }

    #[Route("/create", methods: ["GET"])]
    public function requirementAddGET(Request $request): Response
    {
        return $this->render("internship-program/requirements/create.html", ["section" => "requirements"]);
    }

    #[Route("/create", methods: ["POST"])]
    public function requirementAddPOST(Request $request): RedirectResponse
    {
        $requirementDTO = new RequirementDTO(
            $request->get("name"),
            $request->get("description"),
            $request->get("type"),
            new \DateTime($request->get("start-date")),
            new \DateTime($request->get("end-before")),
            $request->get("repeat-interval")
        );
        $this->requirementService->createRequirement($requirementDTO);
        return $this->redirect("/internship-program/requirements");
    }
}