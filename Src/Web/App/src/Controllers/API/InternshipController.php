<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/internships")]
class InternshipController
{
    public function __construct(
        private InternshipService $internshipService,
    ) {
    }

    #[Route("/{id}", methods: ["GET"], requirements: ['id' => '\d+'])]
    public function internship(int $id): Response
    {
        $internship = $this->internshipService->getInternshipById($id);
        if ($internship) {
            $data = [
                "title" => $internship->getTitle(),
                "description" => $internship->getDescription(),
            ];
            return new Response(json_encode($data), 200, ["Content-Type" => "application/json"]);
        }
        return new Response(null, 404);
    }
}