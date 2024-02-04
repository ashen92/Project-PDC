<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Models\InternshipCycle;
use App\Services\InternMonitoringService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
readonly class InternMonitoringAPIController
{
    public function __construct(
        private InternMonitoringService $internshipMonitoringService,
    ) {

    }

    #[Route('/internship-monitoring/students', methods: ['GET'])]
    public function monitoring(?InternshipCycle $cycle): Response
    {
        $students = $this->internshipMonitoringService->getStudents($cycle->getId());
        return new Response(json_encode($students), 200, ['Content-Type' => 'application/json']);
    }
}