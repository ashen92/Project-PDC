<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Exceptions\EntityNotFound;
use App\Interfaces\IFileStorageService;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\Type;
use App\Models\UserRequirement;
use App\Repositories\RequirementRepository;
use Exception;

readonly class RequirementService
{
    public function __construct(
        private RequirementRepository $requirementRepo,
        private InternshipProgramService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    /**
     * @throws \App\Exceptions\EntityNotFound
     */
    public function getRequirement(int $id): array
    {
        $r = $this->requirementRepo->findRequirement($id);
        if ($r === false)
            throw new EntityNotFound('Requirement not found');

        return $r;
    }

    public function getRequirements(int $cycleId): array
    {
        return $this->requirementRepo->findAllRequirements($cycleId);
    }

    public function getUserRequirement(int $id): ?UserRequirement
    {
        return $this->requirementRepo->findUserRequirement($id);
    }

    public function getActiveUserRequirementsForUser(int $cycleId, int $userId): array
    {
        return $this->requirementRepo->findUserRequirementsToBeCompleted($cycleId, $userId);
    }

    public function createRequirement(CreateRequirementDTO $reqDTO): bool
    {
        $this->requirementRepo->beginTransaction();
        try {
            $cycle = $this->internshipCycleService->getLatestCycle();
            $reqId = $this->requirementRepo
                ->createRequirement($cycle->getId(), $reqDTO);

            if ($reqDTO->requirementType === Type::ONE_TIME) {
                $this->requirementRepo
                    ->createOneTimeUserRequirements($reqId, $cycle->getStudentGroupId());
            } else {
                // TODO
                // $this->createRecurringUserRequirements($reqId);
            }

            $this->requirementRepo->commit();
            return true;
        } catch (Exception $e) {
            $this->requirementRepo->rollBack();
            throw $e;
        }
    }

    public function completeUserRequirement(UserRequirementFulfillmentDTO $dto): bool
    {
        $ur = $this->requirementRepo->findUserRequirement($dto->userRequirementId);
        if (!$ur) {
            throw new Exception("User requirement not found");
        }

        if ($ur->getFulfillMethod() === FulFillMethod::FILE_UPLOAD) {
            $files = $this->fileStorageService->upload($dto->files);

            if ($files) {
                return $this->requirementRepo
                    ->fulfillUserRequirement($dto->userRequirementId, $files);
            }

            // TODO: Handle file upload failure
            return false;
        }

        return $this->requirementRepo
            ->fulfillUserRequirement($dto->userRequirementId, textResponse: $dto->textResponse);
    }
}