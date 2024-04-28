<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\InternshipProgram\CreateApplication;
use App\Repositories\ApplicationRepository;
use App\Repositories\UserRepository;

final readonly class ApplicationService
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private FileStorageService $fileStorageService,
        private RequirementService $requirementService,
        private InternshipProgramService $internshipProgramService,
        private UserRepository $userRepository,
    ) {

    }

    public function createApplication(int $cycleId, CreateApplication $createApplication): bool
    {
        $user = $this->userRepository->findUser($createApplication->userId);
        if ($user === null) {
            throw new \BadMethodCallException('User not found', 1004);
        }

        if ($createApplication->internshipId === null && $createApplication->jobRoleId === null) {
            throw new \InvalidArgumentException('Either internshipId or jobRoleId must be provided', 1005);
        }

        if ($createApplication->internshipId !== null && $createApplication->jobRoleId !== null) {
            throw new \InvalidArgumentException('Only one of internshipId or jobRoleId must be provided', 1006);
        }

        if ($createApplication->jobRoleId === null) {
            $maxApplications = $this->internshipProgramService->valueOfSetting('MaxInternshipApplications');
            $studentApplications = $this->applicationRepository->countInternshipApplicationsByStudent($cycleId, $createApplication->userId);

            if ($studentApplications >= $maxApplications) {
                throw new \InvalidArgumentException('Maximum number of applications reached', 1001);
            }
        } else {
            $maxApplications = $this->internshipProgramService->valueOfSetting('MaxJobRoleApplications');
            $studentApplications = $this->applicationRepository->countJobRoleApplicationsByStudent($cycleId, $createApplication->userId);

            if ($studentApplications >= $maxApplications) {
                throw new \InvalidArgumentException('Maximum number of applications reached', 1001);
            }
        }

        $fileUploadResponse = $this->fileStorageService->upload($createApplication->files);
        if (!$fileUploadResponse) {
            return false;
        }

        return $this->applicationRepository->createApplication(
            $createApplication->userId,
            $fileUploadResponse,
            $createApplication->internshipId,
            $createApplication->jobRoleId
        );
    }

    public function removeApplication(int $userId, ?int $applicationId, ?int $internshipId, ?int $jobRoleId): bool
    {
        return $this->applicationRepository->deleteApplication($userId, $applicationId, $internshipId, $jobRoleId);
    }

    public function getJobRoleApplications(int $jobRoleId): array
    {
        return $this->applicationRepository->findAllApplicationsByJobRole($jobRoleId);
    }

    public function countInternshipApplicationsByStudent(int $cycleId, int $studentId): int
    {
        return $this->applicationRepository->countInternshipApplicationsByStudent($cycleId, $studentId);
    }

    public function hire(int $cycleId, int $partnerId, ?int $applicationId = null, ?int $candidateId = null): bool
    {
        if ($applicationId === null && $candidateId === null)
            throw new \InvalidArgumentException('Either applicationId or candidateId must be provided');

        if ($applicationId !== null && $candidateId !== null)
            throw new \InvalidArgumentException('Only one of applicationId or candidateId must be provided');

        if ($this->applicationRepository->isIntern($cycleId, $candidateId, $applicationId))
            throw new \InvalidArgumentException('Student is already an intern', 1000);

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

    public function getJobRolesAppliedTo(int $cycleId, int $studentId): array
    {
        return $this->applicationRepository->findJobRolesAppliedTo($cycleId, $studentId);
    }
}