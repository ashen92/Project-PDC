<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Company;
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
        return $this->render("internships/cycle.html");
    }

    #[Route("/show", name: "show")]
    public function show(Request $request): Response
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

        return $this->render(
            "internships/show.html",
            array_merge(
                ["jobRoles" => $jobRoles],
                ["companies" => $companies],
                ["queryJobRoles" => $queryParams["Job_Role"] ?? []],
                ["queryCompanies" => $queryParams["Company"] ?? []],
                ["internshipStatus" => $internshipStatus],
                ["queryInternshipStatus" => $queryParams["Internship_Status"] ?? []],
            )
        );
    }

    #[Route("/show/{id}", name: "show_one")]
    public function show_one(int $id): Response
    {
        return $this->render("internships/show_one.html", ["id" => $id]);
    }

    #[Route("/add", name: "add")]
    public function add(): Response
    {
        return $this->render("internships/add.html");
    }
}