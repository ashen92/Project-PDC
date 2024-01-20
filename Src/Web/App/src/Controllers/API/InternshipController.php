<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Interfaces\IInternshipService;
use App\Security\Identity;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/internships")]
class InternshipController
{
    public function __construct(
        private IInternshipService $internshipService,
    ) {
    }

    #[Route("/{id}", methods: ["GET"], requirements: ['id' => '\d+'])]
    public function internship(Request $request, Identity $identity, int $id): Response
    {
        $internship = $this->internshipService->getInternship($id);
        if ($internship) {
            $data = [
                "title" => $internship->getTitle(),
                "description" => $internship->getDescription(),
            ];

            if ($identity->hasRole(Role::InternshipProgram_Student)) {
                $userId = $request->getSession()->get("user_id");
                $data["hasApplied"] = $this->internshipService->hasAppliedToInternship($id, $userId);
            }

            return new Response(json_encode($data), 200, ["Content-Type" => "application/json"]);
        }
        return new Response(null, 404);
    }

    #[Route("/{id}/apply", methods: ["PUT"], requirements: ['id' => '\d+'])]
    public function apply(Request $request, int $id): Response
    {
        // TODO: Validate

        $this->internshipService
            ->apply($id, (int) $request->getSession()->get("user_id"));
        return new Response(null, 204);
    }

    #[Route("/{id}/apply", methods: ["DELETE"], requirements: ['id' => '\d+'])]
    public function cancelApplication(Request $request, int $id): Response
    {
        // TODO: Validate

        $this->internshipService
            ->undoApply($id, (int) $request->getSession()->get("user_id"));
        return new Response(null, 204);
    }
}