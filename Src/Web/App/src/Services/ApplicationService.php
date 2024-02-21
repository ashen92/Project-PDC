<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Repositories\ApplicationRepository;
use BadMethodCallException;

final readonly class ApplicationService
{
    public function __construct(
        private ApplicationRepository $applicationRepository
    ) {

    }

    public function hire(
        int $applicantId,
        ?int $partnerId,
        ?int $adminId,
        ?int $applicationId,
        ?int $organizationId = null,
    ): bool {
        if ($adminId) {
            if (!$organizationId || $partnerId) {
                throw new BadMethodCallException("'partnerId' must be null and 'organizationId' must be set when Admin Id is set");
            }
        }
        if ($partnerId) {
            if ($adminId || $organizationId) {
                throw new BadMethodCallException("'adminId' must be null and 'organizationId' must be null when Partner Id is set");
            }
        }

        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();
        try {
            $this->applicationRepository->createIntern(
                $applicantId,
                $adminId ?? $partnerId,
                $organizationId,
                $applicationId
            );

            if ($applicationId) {
                $this->applicationRepository
                    ->updateApplicationStatus($applicationId, Application\Status::Hired);
            }

            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }

    public function reject(int $applicantId, int $applicationId): bool
    {
        // TODO: Check if parameters exist

        $this->applicationRepository->beginTransaction();

        try {
            $this->applicationRepository->deleteInternIfExists($applicantId, $applicationId);
            $this->applicationRepository
                ->updateApplicationStatus($applicationId, Application\Status::Rejected);
            return $this->applicationRepository->commit();
        } catch (\Exception $e) {
            $this->applicationRepository->rollback();
            throw $e;
        }
    }
}