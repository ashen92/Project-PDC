<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Repositories\ApplicationRepository;

final readonly class ApplicationService
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private FileStorageService $fileStorageService,
        private RequirementService $requirementService,
    ) {

    }

    public function hire(int $cycleId, int $partnerId, ?int $applicationId = null, ?int $candidateId = null): bool
    {
        if ($applicationId === null && $candidateId === null) {
            throw new \InvalidArgumentException('Either applicationId or candidateId must be provided');
        }

        if ($applicationId !== null && $candidateId !== null) {
            throw new \InvalidArgumentException('Only one of applicationId or candidateId must be provided');
        }

        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();
        try {
            if ($applicationId) {
                $application = $this->applicationRepository->findApplication($applicationId);
                $this->applicationRepository->createIntern(
                    $cycleId,
                    $application['user_id'],
                    $partnerId,
                    null,
                    $applicationId
                );

                $this->applicationRepository
                    ->updateApplicationStatus($applicationId, Application\Status::Hired);
                $this->requirementService->createUserRequirements($application['user_id']);
            } else {
                $this->applicationRepository->createIntern(
                    $cycleId,
                    $candidateId,
                    $partnerId,
                    null,
                    null
                );
                $this->requirementService->createUserRequirements($candidateId);
            }

            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }

    public function cancelHire(int $studentId): bool
    {
        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();
        try {
            $this->applicationRepository->deleteIntern($studentId);
            $this->requirementService->removeUserRequirements($studentId);
            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }

    public function reject(int $applicationId): bool
    {
        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();
        try {
            $application = $this->applicationRepository->findApplication($applicationId);
            $this->applicationRepository->deleteInternIfExists($application['user_id'], $applicationId);
            $this->applicationRepository
                ->updateApplicationStatus($applicationId, Application\Status::Rejected);
            $this->requirementService->removeUserRequirements($application['user_id']);
            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }

    public function resetApplicationStatus(int $applicationId): bool
    {
        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();
        try {
            $application = $this->applicationRepository->findApplication($applicationId);
            $this->applicationRepository->deleteInternIfExists($application['user_id'], $applicationId);
            $this->applicationRepository
                ->updateApplicationStatus($applicationId, Application\Status::Pending);
            $this->requirementService->removeUserRequirements($application['user_id']);
            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }

    public function getStudentApplications(int $studentId): array
    {
        $applications = $this->applicationRepository->findAllApplicationsByStudent($studentId);
        foreach ($applications as &$application) {
            $application['fileIds'] = json_decode($application['fileIds']);
        }
        return $applications;
    }

    public function getApplicationFile(int $applicationId, int $fileId): ?array
    {
        $fileMetadata = $this->applicationRepository->findApplicationFile($applicationId, $fileId);

        if ($fileMetadata === null) {
            return null;
        }

        $file = $this->fileStorageService->get($fileMetadata['path']);
        $file['name'] = $fileMetadata['name'];
        return $file;
    }
}