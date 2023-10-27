<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Company;
use App\Entities\Internship;
use App\Entities\JobRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/internship-program", name: "internship_program_")]
class InternshipProgramController extends PageControllerBase
{
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
        $jobRoles = [
            new JobRole(1, "Software Engineer"),
            new JobRole(2, "Data Scientist"),
            new JobRole(3, "Network Administrator")
        ];

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

        $internships = [
            new Internship(
                1,
                "Internship Name or Position. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate modi",
                "Company 1"
            ),
            new Internship(
                2,
                "Internship Name or Position. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate modi",
                "Company 1"
            ),
            new Internship(
                3,
                "Internship Name or Position. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate modi",
                "Company 1"
            ),
            new Internship(
                4,
                "Internship Name or Position. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate modi",
                "Company 1"
            ),
            new Internship(
                5,
                "Internship Name or Position. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate modi",
                "Company 1"
            ),
        ];

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
        return $this->render("internships/home.html", ["section" => "home"]);
    }

    #[Route("/cycle/create", name: "cycle_create")]
    public function cycleCreate(Request $request): Response
    {
        return $this->render("internships/cycle/create.html", ["section" => "home"]);
    }

    #[Route("/monitoring", name: "monitoring")]
    public function monitoring(Request $request): Response
    {
        return $this->render("internships/monitoring.html", ["section" => "monitoring"]);
    }

    #[Route("/documents", name: "documents")]
    public function cycleDocuments(Request $request): Response
    {
        return $this->render("internships/documents.html", ["section" => "documents"]);
    }

    #[Route("/feedback", name: "feedback")]
    public function cycleFeedback(Request $request): Response
    {
        return $this->render("internships/feedback.html", ["section" => "feedback"]);
    }

    #[Route("/internships", name: "internships")]
    public function internships(Request $request): Response
    {
        $data = $this->tempGetInternshipsData();

        $queryParams = $request->query->all();

        return $this->render(
            "internships/internships.html",
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

    #[Route("/show/{id}", name: "internship")]
    public function internship(int $id): Response
    {
        return $this->render("internships/internship/internship.html", ["id" => $id]);
    }

    #[Route("/show/{id}/applicants", name: "applicants")]
    public function internshipApplicants(int $id): Response
    {
        return $this->render("internships/internship/applicants.html", [
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

    #[Route("/show/{id}/edit", name: "edit")]
    public function edit(int $id): Response
    {
        return $this->render("internships/internship/edit.html");
    }

    #[Route("/add", name: "add")]
    public function add(): Response
    {
        return $this->render("internships/internship/add.html");
    }
}