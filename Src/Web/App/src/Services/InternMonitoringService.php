<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\InternMonitoringRepository;

class InternMonitoringService
{
    public function __construct(
        private InternMonitoringRepository $internMonitoringRepo,
    ) {

    }

    public function getStudents(int $cycleId): array
    {
        return $this->internMonitoringRepo->findStudents($cycleId);
    }

    public function getUserRequirements(int $cycleId, int $requirementId): array
    {
        return $this->internMonitoringRepo->findUserRequirements($cycleId, $requirementId);
    }
}