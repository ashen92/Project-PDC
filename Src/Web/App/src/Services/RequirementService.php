<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Entities\Requirement;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IRequirementService;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\Type;
use App\Repositories\RequirementRepository;
use DateInterval;

class RequirementService implements IRequirementService
{
    public function __construct(
        private RequirementRepository $requirementRepository,
        private InternshipCycleService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    #[\Override] public function getRequirement(int $id): ?\App\Models\Requirement
    {
        return $this->requirementRepository->findRequirement($id);
    }

    #[\Override] public function getRequirements(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null)
            $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();

        if ($internshipCycleId === null)
            return [];

        return $this->requirementRepository->findAllRequirements($internshipCycleId);
    }

    #[\Override] public function getUserRequirement(int $id): ?\App\Models\UserRequirement
    {
        return $this->requirementRepository->findUserRequirement($id);
    }

    #[\Override] public function getUserRequirements(
        ?int $internshipCycleId = null,
        ?int $requirementId = null,
        ?int $userId = null,
        ?string $status = null
    ): array {
        if ($internshipCycleId === null)
            $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();

        if ($internshipCycleId === null)
            return [];

        $criteria = [];

        if ($requirementId !== null) {
            $criteria["requirement"] = $requirementId;
        }

        if ($userId !== null) {
            $criteria["user"] = $userId;
        }

        if ($status !== null) {
            $criteria["status"] = $status;
        }

        return $this->requirementRepository->findAllUserRequirements($criteria);
    }

    #[\Override] public function createRequirement(CreateRequirementDTO $requirementDTO): void
    {
        $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();
        $requirement = $this->requirementRepository
            ->createRequirement($requirementDTO, $internshipCycleId);
        $this->createUserRequirements($requirement);
    }

    private function createUserRequirements(Requirement $requirement): void
    {
        if ($requirement->getRequirementType() === Type::ONE_TIME) {
            $this->createOneTimeUserRequirements($requirement);
        } else {
            $this->createRecurringUserRequirements($requirement);
        }
    }

    private function createOneTimeUserRequirements(Requirement $requirement): void
    {
        $internshipCycle = $requirement->getInternshipCycle();

        foreach ($internshipCycle->getStudentGroup()->getUsers() as $user) {
            $this->requirementRepository->createUserRequirement($requirement, $user);
        }
    }

    private function createRecurringUserRequirements(Requirement $requirement): void
    {
        $repeatInterval = $requirement->getRepeatInterval();
        $repeatDuration = $requirement->getRepeatInterval()->toDuration();

        $iterationDate = $requirement->getStartDate();
        $endDate = $iterationDate->add(new DateInterval(Requirement::MAXIMUM_REPEAT_DURATION));

        $urDTOs = [];

        while ($iterationDate < $endDate) {
            $urDTOs[] = new CreateUserRequirementDTO(
                $iterationDate,
                $repeatInterval,
            );
            $iterationDate = $iterationDate->add(new DateInterval($repeatDuration));
        }

        $internshipCycle = $requirement->getInternshipCycle();

        foreach ($internshipCycle->getStudentGroup()->getUsers() as $user) {
            foreach ($urDTOs as $urDTO) {
                $this->requirementRepository
                    ->createUserRequirementFromDTO($requirement, $user, $urDTO);
            }
        }
    }

    #[\Override] public function completeUserRequirement(UserRequirementFulfillmentDTO $dto): bool
    {
        $ur = $this->requirementRepository->findUserRequirement($dto->userRequirementId);

        if (!$ur) {
            return false;

            // TODO: Handle user requirement not found
        }

        if ($ur->getFulfillMethod() === FulFillMethod::FILE_UPLOAD) {
            $files = $this->fileStorageService->upload($dto->files);

            if ($files) {
                $ur->fulfill(filePaths: $files);
            }

            // TODO: Handle file upload failure
        }

        if ($ur->getFulfillMethod() === FulFillMethod::TEXT_INPUT) {
            $ur->fulfill(textResponse: $dto->textResponse);
        }
        $this->requirementRepository->updateUserRequirement($ur);
        return true;
    }
}