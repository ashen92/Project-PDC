<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\InternshipCycle;
use App\Security\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use App\Services\InternMonitoringService;
use App\Services\RequirementService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole('InternshipProgramAdmin')]
#[Route('/internship-program/monitoring')]
class InternMonitoringController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly InternMonitoringService $internMonitoringService,
        private readonly RequirementService $requirementService,
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route('', methods: ['GET'])]
    public function monitoring(InternshipCycle $cycle): Response
    {
        return $this->render(
            'internship-program/monitoring/home.html',
            [
                'section' => 'monitoring',
                'requirements' => $this->requirementService->getRequirements($cycle->getId())
            ]
        );
    }

    #[Route('/students', methods: ['GET'])]
    public function monitoringStudentUsers(InternshipCycle $cycle): Response
    {
        return $this->render(
            'internship-program/monitoring/students.html',
            [
                'section' => 'monitoring',
                'students' => $this->internMonitoringService->getStudents($cycle->getId()),
            ]
        );
    }

    #[Route('/students/{studentId}', requirements: ['studentId' => '\d+'], methods: ['GET'])]
    public function monitoringStudentUser(?InternshipCycle $cycle, int $studentId): Response
    {
        return $this->render(
            'internship-program/monitoring/student.html',
            [
                'section' => 'monitoring',
                'studentId' => $studentId,
                'summary' => $this->internMonitoringService->getStudentSummary($cycle->getId(), $studentId),
            ]
        );
    }

    #[Route('/submissions', methods: ['GET'])]
    public function requirementSubmissions(Request $request, ?InternshipCycle $cycle): Response|RedirectResponse
    {
        $requirementId = $request->get('r');
        if ($requirementId === null) {
            return new RedirectResponse('/internship-program/monitoring');
        }

        // TODO: Validate

        $requirement = $this->requirementService->getRequirement((int) $requirementId);

        return $this->render(
            'internship-program/monitoring/submissions.html',
            [
                'section' => 'monitoring',
                'requirement' => $requirement,
            ]
        );
    }

    #[Route('/interns', methods: ['GET'])]
    public function interns(): Response
    {
        return $this->render(
            'internship-program/monitoring/interns.html',
            [
                'section' => 'monitoring',
            ]
        );
    }
}