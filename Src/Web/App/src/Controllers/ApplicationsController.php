<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\Models\InternshipCycle;
use App\Security\Role;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/internship-program")]
class ApplicationsController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private readonly InternshipService $internshipService
    ) {
        parent::__construct($twig);
    }

    #[Route(["/applications"])]
    public function applications(): Response
    {
        return $this->render(
            "internship-program/applications/home.html",
            ["section" => "applications"]
        );
    }

    #[RequiredRole(Role::InternshipProgram_Partner_Admin)]
    #[Route("/applicants", methods: ["GET"])]
    public function applicants(Request $request, ?InternshipCycle $cycle): Response
    {
        return $this->render(
            "internship-program/applicants.html",
            [
                "section" => "applicants",
                'internships' => $this->internshipService
                    ->getInternships($cycle->getId(), $request->getSession()->get('user_id')),
            ]
        );
    }

    #[RequiredRole(Role::InternshipProgram_Partner_Admin)]
    #[Route("/applicants/applications", methods: ["GET"])]
    public function applicantsApplications(Request $request, ?InternshipCycle $cycle): Response
    {
        return new Response("Hello World!");
    }
}