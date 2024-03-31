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
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobCollectionStart: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-collection/end', methods: ['PATCH'])]
    public function endJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobCollectionEnd: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-collection/restart', methods: ['PATCH'])]
    public function undoEndJobCollection(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->resetInternshipCycleDates(
            $cycle->getId(),
            resetJobCollectionEnd: true
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/1/start', methods: ['PATCH'])]
    public function startJobHuntRound1(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobHuntRound1Start: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/1/end', methods: ['PATCH'])]
    public function endJobHuntRound1(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobHuntRound1End: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/1/restart', methods: ['PATCH'])]
    public function undoEndJobHuntRound1(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->resetInternshipCycleDates(
            $cycle->getId(),
            resetJobHuntRound1End: true
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/2/start', methods: ['PATCH'])]
    public function startJobHuntRound2(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobHuntRound2Start: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/2/end', methods: ['PATCH'])]
    public function endJobHuntRound2(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->modifyInternshipCycleDates(
            $cycle->getId(),
            jobHuntRound2End: new \DateTimeImmutable()
        );
        return new Response('', 204);
    }

    #[Route('/job-hunt/round/2/restart', methods: ['PATCH'])]
    public function undoEndJobHuntRound2(?InternshipCycle $cycle): Response
    {
        $this->internshipProgramService->resetInternshipCycleDates(
            $cycle->getId(),
            resetJobHuntRound2End: true
        );
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