<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IInternshipService;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/internship-program/internships")]
class InternshipController extends PageControllerBase
{
    private IInternshipService $internshipService;
    private IUserService $userService;

    public function __construct(
        Environment $twig,
        IInternshipService $internshipService,
        IUserService $userService,
    ) {
        $this->internshipService = $internshipService;
        $this->userService = $userService;
        parent::__construct($twig);
    }

    #[Route(["", "/"])]
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
            )
        );
    }

    #[Route("/{id}", methods: ["GET"], requirements: ['id' => '\d+'])]
    public function internship(int $id): Response|RedirectResponse
    {
        $internship = $this->internshipService->getInternshipById($id);
        if ($internship) {
            return new Response(json_encode($internship), 200, ["Content-Type" => "application/json"]);
        }
        return $this->redirect("/internship-program");
    }

    #[Route("/{id}", methods: ["DELETE"], requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
    {
        $this->internshipService->deleteInternshipById($id);
        return new Response(null, 204);
    }

    #[Route("/{id}/applicants")]
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

    #[Route("/{id}/modify", methods: ["GET"])]
    public function editGET(int $id): Response
    {
        return $this->render("internship-program/internship/modify.html", [
            "section" => "internships",
            "internship" => $this->internshipService->getInternshipById($id)
        ]);
    }

    #[Route("/{id}/modify", methods: ["POST"])]
    public function editPOST(Request $request): RedirectResponse
    {
        $this->internshipService->updateInternship(
            (int) $request->get("id"),
            $request->get("title"),
            $request->get("description")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/create", methods: ["GET"])]
    public function addGET(): Response
    {
        return $this->render("internship-program/internship/create.html", ["section" => "internships"]);
    }

    #[Route("/create", methods: ["POST"])]
    public function addPOST(Request $request): RedirectResponse
    {
        $this->internshipService->addInternship(
            $request->get("title"),
            $request->get("description"),
            (int) $request->getSession()->get("user_id")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/{id}/apply")]
    public function apply(Request $request, int $id): Response
    {
        $this->internshipService->applyToInternship($id, (int) $request->getSession()->get("user_id"));
        return $this->redirect("/internship-program/internships");
    }
}