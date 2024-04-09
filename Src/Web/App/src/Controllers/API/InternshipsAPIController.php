<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Controllers\ControllerBase;
use App\Security\Attributes\RequiredRole;
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
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function internship(Request $request, int $id): Response
    {
        $internship = $this->internshipService->getInternship($id);
        if ($internship) {
            $data = [
                'title' => $internship->getTitle(),
                'description' => $internship->getDescription(),
            ];

            if ($this->hasRole('InternshipProgramStudent')) {
                $userId = $request->getSession()->get('user_id');
                $data['hasApplied'] = $this->internshipService->hasAppliedToInternship($id, $userId);
            }

            return new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
        }
        return new Response(null, 404);
    }

    #[Route('/{id}/apply', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function apply(Request $request, int $id): Response
    {
        // TODO: Validate

        $this->internshipService
            ->apply($id, (int) $request->getSession()->get('user_id'));
        return new Response(null, 204);
    }

    #[Route('/{id}/apply', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function cancelApplication(Request $request, int $id): Response
    {
        // TODO: Validate

        $this->internshipService
            ->undoApply($id, (int) $request->getSession()->get('user_id'));
        return new Response(null, 204);
    }

    #[RequiredRole([
        'InternshipProgramAdmin',
        'InternshipProgramPartnerAdmin'
    ])]
    #[Route('/{id}/applications', methods: ['GET'])]
    public function internshipApplications(int $id): Response
    {
        return new Response(
            json_encode($this->internshipService->getApplications($id)),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}