<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Models\InternshipCycle;
use App\Services\InternMonitoringService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/intern-monitoring')]
readonly class InternMonitoringAPIController
{
    public function __construct(
        private InternMonitoringService $internMonitoringService,
    ) {
    }

    #[Route('/students', methods: ['GET'])]
    public function monitoring(?InternshipCycle $cycle): Response
    {
        $students = $this->internMonitoringService->getStudents($cycle->getId());
        return new Response(json_encode($students), 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/requirements/{id}/user-requirements', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function userRequirements(?InternshipCycle $cycle, int $id): Response
    {
        $ur = $this->internMonitoringService->getUserRequirements($cycle->getId(), $id);
        return new Response(json_encode($ur), 200, ['Content-Type' => 'application/json']);
    }
}