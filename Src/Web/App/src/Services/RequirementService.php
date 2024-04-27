<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Requirement;
use App\Models\Requirement\FulFillMethod;
use App\Models\UserRequirement;
use App\Repositories\RequirementRepository;
use DateTimeImmutable;
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
            $this->requirementRepo
                ->createRequirement($cycle->getId(), $reqDTO);
            return $this->requirementRepo->commit();
        } catch (Exception $e) {
            $this->requirementRepo->rollBack();
            throw $e;
        }
    }

    public function createUserRequirements(int $userId): bool
    {
        $cycle = $this->internshipCycleService->getLatestCycle();
        $requirements = $this->requirementRepo->findAllRequirements($cycle->getId());

        foreach ($requirements as $req) {
            if ($req->getStartWeek()->d === 0) {

                $now = new DateTimeImmutable();
                $startDate = $now->modify('tomorrow midnight');
                $endDate = $startDate->add($req->getDurationWeeks());

                $this->requirementRepo->createUserRequirement($req, $userId, $startDate, $endDate);
            }
        }
        return true;
    }

    public function removeUserRequirements(int $userId): bool
    {
        return $this->requirementRepo->deleteUserRequirements($userId);
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