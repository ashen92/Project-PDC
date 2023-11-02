<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Company;
use App\Entities\Internship;
use App\Entities\JobRole;
use App\Interfaces\IInternshipService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
    private IInternshipService $internshipService;

    public function __construct(
        Environment $twig,
        IInternshipService $internshipService
    ) {
        $this->internshipService = $internshipService;
        parent::__construct($twig);
    }

    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "/internship-program";
    }

    private function tempGetInternshipsData()
    {
        $jobRoles = [];

        $companies = [
            new Company(1, "LSEG"),
            new Company(2, "WSO2"),
            new Company(3, "IFS")
        ];

        $internshipStatus = [
            "Approved",
            "Pending Approval",
            "Rejected"
        ];

        $internships = $this->internshipService->getInternships();

        return [
            "jobRoles" => $jobRoles,
            "companies" => $companies,
            "internshipStatus" => $internshipStatus,
            "internships" => $internships
        ];
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

    #[Route("/monitoring", name: "monitoring")]
    public function monitoring(Request $request): Response
    {
        return $this->render("internship-program/monitoring.html", ["section" => "monitoring"]);
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
        $data = $this->tempGetInternshipsData();

        $queryParams = $request->query->all();

        return $this->render(
            "internship-program/internships.html",
            array_merge(
                ["section" => "internships"],
                ["internships" => $data["internships"]],
                ["jobRoles" => $data["jobRoles"]],
                ["companies" => $data["companies"]],
                ["internshipStatus" => $data["internshipStatus"]],
                ["queryJobRoles" => $queryParams["Job_Role"] ?? []],
                ["queryCompanies" => $queryParams["Company"] ?? []],
                ["queryInternshipStatus" => $queryParams["Internship_Status"] ?? []],
            )
        );
    }

    #[Route("/internship/{id}", name: "internship", requirements: ['id' => '\d+'])]
    public function internship(int $id): Response
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

    #[Route("/internship/{id}/edit", name: "edit")]
    public function edit(int $id): Response
    {
        return $this->render("internship-program/internship/edit.html", ["section" => "internships"]);
    }

    #[Route("/internship/create", methods: ["GET"])]
    public function add(): Response
    {
        return $this->render("internship-program/internship/add.html", ["section" => "internships"]);
    }

    #[Route("/internship/create", methods: ["POST"])]
    public function addPost(Request $request): Response
    {
        $this->internshipService->addInternship(
            $request->get("title"),
            $request->get("description"),
            (int) $request->getSession()->get("user_id")
        );
        return $this->redirect("/internship-program/internships");
    }

    #[Route("/internship/{id}/delete", name: "delete")]
    public function delete(Request $request, int $id): Response
    {
        $this->internshipService->deleteInternshipById($id);
        return $this->redirect("/internship-program/internships");
    }
}