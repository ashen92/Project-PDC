<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\InternshipCycle;
use App\Security\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use App\Services\ApplicationService;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/internship-program')]
class ApplicationsController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly InternshipService $internshipService,
        private readonly ApplicationService $applicationService,
    ) {
        parent::__construct($twig, $authzService);
    }

    #[RequiredRole('InternshipProgramStudent')]
    #[Route(['/applications'])]
    public function applications(Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        return $this->render(
            'internship-program/applications/student/home.html',
            [
                'section' => 'applications',
                'applications' => $this->applicationService->getStudentApplications($userId),
            ]
        );
    }

    #[Route('/applications/{applicationId}/files/{fileId}',
        requirements: ['applicationId' => '\d+', 'fileId' => '\d+'],
        methods: ['GET'])
    ]
    public function applicationFile(int $applicationId, int $fileId): Response
    {
        // TODO: Validate

        $file = $this->applicationService->getApplicationFile($applicationId, $fileId);
        if ($file === null) {
            return new Response(null, 404);
        }

        $response = new Response();
        $response->headers->set('Content-Type', $file['mimeType']);
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file['name'])
        );
        $response->setContent($file['content']);
        return $response;
    }


    #[RequiredRole('InternshipProgramPartnerAdmin')]
    #[Route('/applicants', methods: ['GET'])]
    public function applicants(Request $request, ?InternshipCycle $cycle): Response
    {
        return $this->render(
            'internship-program/applicants/home.html',
            [
                'section' => 'applicants',
                'internships' => $this->internshipService
                    ->getInternships($cycle->getId(), $request->getSession()->get('user_id')),
            ]
        );
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/applicants/applications', methods: ['GET'])]
    public function applicantsApplications(Request $request): Response
    {
        $internshipId = $request->query->getInt('i');
        // TODO: Validate

        if (!$internshipId || $internshipId < 1) {
            return $this->redirect('/internship-program/applicants');
        }

        $internship = $this->internshipService->getInternship($internshipId);
        return $this->render(
            'internship-program/applicants/applications.html',
            [
                'section' => 'applicants',
                'internship' => $internship,
                'applications' => $this->internshipService->getApplications($internship->getId()),
            ]
        );
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/applicants/applications/{id}/hire', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function applicantHire(Request $request, int $id): Response
    {
        $userId = $request->getSession()->get('user_id');
        if ($this->applicationService->hire($userId, $id)) {
            return new Response(null, 204);
        }
        return new Response(null, 400);
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/applicants/applications/{id}/reject', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function applicantReject(Request $request, int $id): Response
    {
        if ($this->applicationService->reject($id)) {
            return new Response(null, 204);
        }
        return new Response(null, 400);
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/applicants/applications/{id}/reset', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function applicantReset(Request $request, int $id): Response
    {
        if ($this->applicationService->resetApplicationStatus($id)) {
            return new Response(null, 204);
        }
        return new Response(null, 400);
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/applicants/applications/{applicationId}/files/{fileId}',
        requirements: ['applicationId' => '\d+', 'fileId' => '\d+'],
        methods: ['GET'])]
    public function applicantFile(Request $request, int $applicationId, int $fileId): Response
    {
        return $this->applicationFile($applicationId, $fileId);
    }
}