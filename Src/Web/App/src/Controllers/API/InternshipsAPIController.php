<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Controllers\ControllerBase;
use App\Repositories\ApplicationRepository;
use App\Security\AuthorizationService;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/api/internships')]
class InternshipsAPIController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private InternshipService $internshipService,
        private ApplicationRepository $applicationRepository,
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function internship(Request $request, int $id): Response
    {
        if ($this->hasRole('InternshipProgramStudent')) {
            $userId = $request->getSession()->get('user_id');
            $res = $this->internshipService->getInternshipDetailsForStudent($id, $userId);
            return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);
        }

        $internship = $this->internshipService->getInternship($id);
        if ($internship) {
            $data = [
                'title' => $internship->getTitle(),
                'description' => $internship->getDescription(),
                'isApproved' => $internship->isApproved(),
                'organizationId' => $internship->getOrganizationId(),
            ];

            return new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
        }
        return new Response(null, 404);
    }

    #[Route('/{internshipId}/applications/{applicationId}',
        requirements: ['internshipId' => '\d+', 'applicationId' => '\d+'],
        methods: ['DELETE'])
    ]
    public function cancelApplication(Request $request, int $internshipId, int $applicationId): Response
    {
        // TODO: Validate

        $this->applicationRepository->deleteApplication(
            $applicationId,
            (int) $request->getSession()->get('user_id'),
            $internshipId,
            null
        );
        return new Response(null, 204);
    }
}