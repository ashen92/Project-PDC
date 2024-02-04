<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\Models\InternshipCycle;
use App\Security\Role;
use App\Services\RequirementService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole(Role::InternshipProgram_Admin)]
#[Route('/internship-program/monitoring')]
class InternMonitoringController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private readonly RequirementService $requirementService,
    ) {
        parent::__construct($twig);
    }

    #[Route('', methods: ['GET'])]
    public function monitoring(): Response
    {
        return $this->render(
            'internship-program/monitoring/home.html',
            [
                'section' => 'monitoring',
                'requirements' => $this->requirementService->getRequirements()
            ]
        );
    }

    #[Route('/students', methods: ['GET'])]
    public function monitoringStudentUsers(): Response
    {
        return $this->render(
            'internship-program/monitoring/students.html',
            [
                'section' => 'monitoring',
                'apiEndpoint' => 'http://localhost:80/api/internship-monitoring/students'
            ]
        );
    }

    #[Route('/submissions', methods: ['GET'])]
    public function requirementSubmissions(Request $request, ?InternshipCycle $cycle): Response|RedirectResponse
    {
        return $this->render(
            'internship-program/monitoring/submissions.html',
            [
                'section' => 'monitoring',
            ]
        );
    }
}