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

#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
    private IInternshipService $internshipService;
    private IUserService $userService;

    private IRequirementService $requirementService;

    public function __construct(
        Environment $twig,
        IInternshipService $internshipService,
        IUserService $userService,
        IRequirementService $requirementService
    ) {
        $this->internshipService = $internshipService;
        $this->userService = $userService;
        $this->requirementService = $requirementService;
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
        return $this->render("internship-program/cycle/create.html", ["section" => "home"]);
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

    #[Route("/requirements", methods: ["GET"])]
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

    #[Route("/requirements/add", methods: ["GET"])]
    public function requirementAddGET(Request $request): Response
    {
        return $this->render("internship-program/requirements/add.html", ["section" => "requirements"]);
    }

    #[Route("/requirements/add", methods: ["POST"])]
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

    #[Route("/internships", name: "internships")]
    public function internships(Request $request): Response
    {
        $userId = $request->getSession()->get("user_id");

        $queryParams = $request->query->all();

        $searchQuery = $queryParams["q"] ?? null;

        $internships = [];

        if ($this->userService->hasRole($userId, "ROLE_PARTNER")) {
            if ($searchQuery) {
                $internships = $this->internshipService->getInternshipsBy($userId, $searchQuery);
            } else {
                $internships = $this->internshipService->getInternshipsByUserId($userId);
            }
        } else {
            if ($searchQuery) {
                $internships = $this->internshipService->getInternshipsBy(null, $searchQuery);
            } else {
                $internships = $this->internshipService->getInternships();
            }
        }

        return $this->render(
            "internship-program/internships.html",
            array_merge(
                ["section" => "internships"],
                ["internships" => $internships],
                ["companies" => array()],
                ["internshipStatus" => array()],
                ["queryCompanies" => $queryParams["Company"] ?? []],
                ["queryInternshipStatus" => $queryParams["Internship_Status"] ?? []],
            )
        );
    }

    #[Route("/internship/{id}", name: "internship", requirements: ['id' => '\d+'])]
    public function internship(int $id): Response|RedirectResponse
    {
        $internship = $this->internshipService->getInternshipById($id);
        if ($internship) {
            return $this->render(
                "internship-program/internship/internship.html",
                [
                    "section" => "internships",
                    "id" => $id,
                    "internship" => $internship
                ]
            );
        }
        return $this->redirect("/internship-program");
    }

    #[Route("/internship/{id}/applicants", name: "applicants")]
    public function internshipApplicants(int $id): Response
    {
        return $this->render("internship-program/internship/applicants.html", [
            "section" => "internships",
            "applicants" => [
                "Ashen",
                "Smith",
                "James",
                "Green",
                "Head",
                "Jimmy",
            ]
        ]);
    }

    #[Route("/internship/{id}/edit", name: "edit_get", methods: ["GET"])]
    public function edit(int $id): Response
    {
        return $this->render("internship-program/internship/edit.html", [
            "section" => "internships",
            "internship" => $this->internshipService->getInternshipById($id)
        ]);
    }

    #[Route("/internship/{id}/edit", name: "edit_post", methods: ["POST"])]
    public function editPost(Request $request): RedirectResponse
    {
        $this->internshipService->updateInternship(
            (int) $request->get("id"),
            $request->get("title"),
            $request->get("description")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/internship/create", name: "add_get", methods: ["GET"])]
    public function add(): Response
    {
        return $this->render("internship-program/internship/add.html", ["section" => "internships"]);
    }

    #[Route("/internship/create", name: "add_post", methods: ["POST"])]
    public function addPost(Request $request): RedirectResponse
    {
        $this->internshipService->addInternship(
            $request->get("title"),
            $request->get("description"),
            (int) $request->getSession()->get("user_id")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/internship/{id}/delete", name: "delete")]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $this->internshipService->deleteInternshipById($id);
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/internship/{id}/apply", name: "apply")]
    public function apply(Request $request, int $id): Response
    {
        $this->internshipService->applyToInternship($id, (int) $request->getSession()->get("user_id"));
        return $this->redirect("/internship-program/internships");
    }
}