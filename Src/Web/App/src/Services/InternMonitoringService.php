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

    public function getUserRequirements(
        int $cycleId,
        int $requirementId,
        int $limit,
        int $offsetBy,
    ): array {
        $urs = $this->internMonitoringRepo->findUserRequirements($cycleId, $requirementId, $limit, $offsetBy);
        foreach ($urs as &$ur) {
            if ($ur['files'] === null) {
                $ur['files'] = [];
                continue;
            }
            $ur['files'] = explode('|', $ur['files']);
            foreach ($ur['files'] as &$file) {
                $file = explode(':', $file);
                $file = [
                    'id' => $file[0],
                    'name' => $file[1],
                    'url' => 'http://localhost:80/api/intern-monitoring/requirements/'
                        . $requirementId . '/user-requirements/'
                        . $ur['id'] . '/submissions/files/' . $file[2],
                ];
            }
        }
        $results["totalCount"] = $this->internMonitoringRepo->countUserRequirements($cycleId, $requirementId);
        $results["data"] = $urs;
        return $results;
    }

    public function getFile(int $cycleId, int $reqId, int $userReqId, string $fileId): ?array
    {
        return $this->fileStorageService->get($fileId);
    }
}