<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Models\InternshipCycle;
use App\Services\InternshipProgramService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/internship-program')]
readonly class InternshipProgramAPIController
{
    private const MAX_PAGE_SIZE = 50;

    public function __construct(
        private InternshipProgramService $internshipProgramService
    ) {
    }

    #[Route('/job-collection/start', methods: ['PATCH'])]
    public function startJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->startJobCollection($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/job-collection/undo', methods: ['PATCH'])]
    public function undoStartJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoStartJobCollection($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/job-collection/end', methods: ['PATCH'])]
    public function endJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->endJobCollection($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/job-collection/restart', methods: ['PATCH'])]
    public function undoEndJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoEndJobCollection($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/applying/start', methods: ['PATCH'])]
    public function startApplying(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->startApplying($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/applying/undo', methods: ['PATCH'])]
    public function undoStartApplying(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoStartApplying($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/applying/end', methods: ['PATCH'])]
    public function endApplying(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->endApplying($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/applying/restart', methods: ['PATCH'])]
    public function undoEndApplying(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoEndApplying($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/interning/start', methods: ['PATCH'])]
    public function startInterning(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->startInterning($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/interning/undo', methods: ['PATCH'])]
    public function undoStartInterning(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoStartInterning($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/interning/end', methods: ['PATCH'])]
    public function endInterning(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->endInterning($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/interning/restart', methods: ['PATCH'])]
    public function undoEndInterning(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->undoEndInterning($cycle->getId());
        return new Response('', 204);
    }

    #[Route('/participants', methods: ['GET'])]
    public function users(Request $request, ?InternshipCycle $cycle): Response
    {
        $page = $request->query->getInt('page', 0);
        // TODO: Validate

        return new Response(
            json_encode(
                $this->internshipProgramService->getParticipants(
                    $cycle->getId(),
                    self::MAX_PAGE_SIZE,
                    $page * self::MAX_PAGE_SIZE
                )
            ),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}