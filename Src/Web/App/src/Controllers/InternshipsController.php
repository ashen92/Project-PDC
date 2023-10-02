<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Company;
use App\Entities\Internship;
use App\Entities\JobRole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/internships", name: "internships_")]
class InternshipsController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "/internships";
    }

    #[Route(["", "/"], name: "home")]
    public function home(Request $request): Response
    {
        return $this->render("internships/home.html");
    }

    #[Route("/cycle", name: "cycle")]
    public function cycle(Request $request): Response
    {
        return $this->render("internships/cycle/cycle.html", ["contentSection" => "cycle"]);
    }

    #[Route("/cycle/create", name: "cycle_create")]
    public function cycleCreate(Request $request): Response
    {
        return $this->render("internships/cycle/create.html", ["contentSection" => "cycle"]);
    }

    #[Route("/cycle/monitoring", name: "cycle_monitoring")]
    public function cycleMonitoring(Request $request): Response
    {
        return $this->render("internships/cycle/monitoring.html", ["contentSection" => "monitoring"]);
    }

    #[Route("/show", name: "internships")]
    public function internships(Request $request): Response
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

        $queryParams = $request->query->all();

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

        return $this->render(
            "internships/internships.html",
            array_merge(
                ["internships" => $internships],
                ["jobRoles" => $jobRoles],
                ["companies" => $companies],
                ["queryJobRoles" => $queryParams["Job_Role"] ?? []],
                ["queryCompanies" => $queryParams["Company"] ?? []],
                ["internshipStatus" => $internshipStatus],
                ["queryInternshipStatus" => $queryParams["Internship_Status"] ?? []],
            )
        );
    }

    #[Route("/show/{id}", name: "internship")]
    public function internship(int $id): Response
    {
        return $this->render("internships/internship.html", ["id" => $id]);
    }

    #[Route("/show/{id}/applicants", name: "internshipApplicants")]
    public function internshipApplicants(int $id): Response
    {
        return $this->render("internships/internship_applicants.html", [
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
        return $this->render("internships/edit.html");
    }

    #[Route("/add", name: "add")]
    public function add(): Response
    {
        return $this->render("internships/add.html");
    }
}