<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Attributes\RequiredRole;
use App\Security\Identity;
use App\Security\Role;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/internships')]
readonly class InternshipsAPIController
{
    public function __construct(
        private InternshipService $internshipService,
    ) {
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function internship(Request $request, Identity $identity, int $id): Response
    {
        $internship = $this->internshipService->getInternship($id);
        if ($internship) {
            $data = [
                'title' => $internship->getTitle(),
                'description' => $internship->getDescription(),
                'applyOnExternalWebsite' => $internship->getApplyOnExternalWebsite(),
            ];

            if ($internship->getApplyOnExternalWebsite()) {
                $data['externalWebsite'] = $internship->getExternalWebsite();
            }

            if ($identity->hasRole(Role::InternshipProgram_Student)) {
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
        Role::InternshipProgram_Admin,
        Role::InternshipProgram_Partner_Admin
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