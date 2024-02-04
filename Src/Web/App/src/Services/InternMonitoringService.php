<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\InternMonitoringRepository;
use App\Repositories\RequirementRepository;

class InternMonitoringService
{
    public function __construct(
        private InternMonitoringRepository $internMonitoringRepo,
        private RequirementRepository $requirementRepo,
    ) {

    }

    public function getStudents(int $cycleId): array
    {
        return $this->internMonitoringRepo->findStudents($cycleId);
    }
}