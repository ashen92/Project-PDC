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

    #[Route(["", "/"], name: "home")]
    public function home(Request $request): Response
    {
        return $this->render("internships/home.html", ["section" => "home"]);
    }

    #[Route("/{section}", name: "sections")]
    public function sections(Request $request, string $section): Response
    {
        if ($section === "home") {
            return $this->render("internships/home.html", ["section" => "home"]);
        }
        if ($section === "internships") {
            $data = $this->tempGetInternshipsData();

            return $this->render(
                "internships/internships.html",
                array_merge(
                    ["internships" => $data["internships"]],
                    ["jobRoles" => $data["jobRoles"]],
                    ["companies" => $data["companies"]],
                    ["internshipStatus" => $data["internshipStatus"]],
                    ["section" => "internships"]
                )
            );
        }
        if ($section === "monitoring") {
            return $this->render("internships/monitoring.html", ["section" => "monitoring"]);
        }

        if ($section === "documents") {
            return $this->render("internships/documents.html", ["section" => "documents"]);
        }

        return $this->render("internships/feedback.html", ["section" => "feedback"]);
    }

    #[Route("/cycle/create", name: "cycle_create")]
    public function cycleCreate(Request $request): Response
    {
        return $this->render("internships/cycle/create.html", ["section" => "home"]);
    }

    #[Route("/cycle/monitoring", name: "cycle_monitoring")]
    public function cycleMonitoring(Request $request): Response
    {
        return $this->render("internships/cycle/monitoring.html", [
            "section" => "monitoring",
            "internship_cycle_status" => "active"
        ]);
    }

    #[Route("/cycle/documents", name: "cycle_documents")]
    public function cycleDocuments(Request $request): Response
    {
        return $this->render("internships/cycle/documents.html", [
            "section" => "documents",
            "internship_cycle_status" => "active"
        ]);
    }

    #[Route("/cycle/feedback", name: "cycle_feedback")]
    public function cycleFeedback(Request $request): Response
    {
        return $this->render("internships/cycle/feedback.html", [
            "section" => "feedback",
            "internship_cycle_status" => "active"
        ]);
    }

    #[Route("/show", name: "internships")]
    public function internships(Request $request): Response
    {
        $data = $this->tempGetInternshipsData();

        $queryParams = $request->query->all();

        return $this->render(
            "internships/internships.html",
            array_merge(
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