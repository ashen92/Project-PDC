<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementCompletionDTO;
use App\Interfaces\IInternshipService;
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
    "ROLE_INTERNSHIP_STUDENT"
])]
#[Route("/internship-program/requirements")]
class RequirementController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private IUserService $userService,
        private IRequirementService $requirementService
    ) {
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
        $fileTypes = $request->get("file-types");
        if (!is_array($fileTypes)) {
            $fileTypes = [$fileTypes];
        }

        $requirementDTO = new CreateRequirementDTO(
            $request->get("name"),
            $request->get("description"),
            $request->get("type"),
            new \DateTime($request->get("start-date")),
            new \DateTime($request->get("end-before")),
            $request->get("repeat-interval"),
            $request->get("fulfill-method"),
            $fileTypes,
            (int) $request->get("max-file-size"),
            (int) $request->get("max-file-count")
        );
        // Validate DTO

        $this->requirementService->createRequirement($requirementDTO);
        return $this->redirect("/internship-program/requirements");
    }

    #[Route("/{id}", methods: ["GET"], requirements: ['id' => '\d+'])]
    public function requirement(Request $request, int $id): Response|RedirectResponse
    {
        if ($this->userService->hasRole($request->getSession()->get("user_id"), "ROLE_ADMIN")) {
            $requirement = $this->requirementService->getRequirement($id);
            if ($requirement) {
                return $this->render(
                    "internship-program/requirements/requirement.html",
                    [
                        "section" => "requirements",
                        "requirement" => $requirement
                    ]
                );
            }
        } else {
            $requirement = $this->requirementService->getUserRequirement($id);
            if ($requirement) {
                return $this->render(
                    "internship-program/requirements/user-requirement.html",
                    [
                        "section" => "requirements",
                        "requirement" => $requirement
                    ]
                );
            }
        }
        return $this->redirect("/internship-program/requirements");
    }

    #[Route("/complete", methods: ["POST"])]
    public function complete(Request $request): Response|RedirectResponse
    {
        $files = $request->files->get("files-to-upload");
        if (!is_array($files)) {
            $files = [$files];
        }
        $urCompletionDTO = new UserRequirementCompletionDTO(
            (int) $request->get("requirement-id"),
            $files
        );
        // Validate DTO

        $this->requirementService->completeUserRequirement($urCompletionDTO);

        return $this->redirect("/internship-program/requirements/{$urCompletionDTO->requirementId}");
    }
}