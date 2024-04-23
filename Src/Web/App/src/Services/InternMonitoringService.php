<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IFileStorageService;
use App\Repositories\InternMonitoringRepository;

class InternMonitoringService
{
    public function __construct(
        private InternMonitoringRepository $internMonitoringRepo,
        private IFileStorageService $fileStorageService,
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

    public function getUserRequirementFile(int $userReqId, int $fileId): ?array
    {
        $fileMetadata = $this->internMonitoringRepo->findUserRequirementFile($userReqId, $fileId);
        $file = $this->fileStorageService->get($fileMetadata['path']);
        if ($file === null) {
            return null;
        }
        $file['name'] = $fileMetadata['name'];
        return $file;
    }

    public function getStudentSummary(int $cycleId, int $studentId): array
    {
        return [
            'student' => $this->internMonitoringRepo->findStudent($studentId),
            'userRequirements' => $this->internMonitoringRepo->getUserRequirementsByUserId($cycleId, $studentId),
        ];
    }
}