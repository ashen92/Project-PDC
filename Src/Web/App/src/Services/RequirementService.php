<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Requirement;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\Type;
use App\Models\UserRequirement;
use App\Models\UserRequirement\Status;
use App\Repositories\RequirementRepository;
use DateInterval;
use Exception;

readonly class RequirementService
{
    public function __construct(
        private RequirementRepository $requirementRepo,
        private InternshipProgramService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    public function getRequirement(int $id): ?Requirement
    {
        return $this->requirementRepo->findRequirement($id);
    }

    /**
     * @return array<Requirement>
     */
    public function getRequirements(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null)
            $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();

        if ($internshipCycleId === null)
            return [];

        return $this->requirementRepo->findAllRequirements($internshipCycleId);
    }

    public function getUserRequirement(int $id): ?UserRequirement
    {
        return $this->requirementRepo->findUserRequirement($id);
    }

    /**
     * @return array<UserRequirement>
     */
    public function getUserRequirements(
        int $cycleId,
        ?int $requirementId = null,
        ?int $userId = null,
        ?Status $status = null
    ): array {
        return $this->requirementRepo->findAllUserRequirements($cycleId, $requirementId, $userId, $status);
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