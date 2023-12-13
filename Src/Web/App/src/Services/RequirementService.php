<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\DTOs\UserRequirementCompletionDTO;
use App\Entities\Requirement;
use App\Entities\UserRequirement;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IRequirementService;
use App\Models\RequirementRepeatInterval;
use App\Models\RequirementType;
use App\Repositories\RequirementRepository;
use DateInterval;
use DateTime;

class RequirementService implements IRequirementService
{
    public function __construct(
        private RequirementRepository $requirementRepository,
        private InternshipCycleService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    public function getRequirement(int $id): ?Requirement
    {
        return $this->requirementRepository->getRequirement($id);
    }

    public function getRequirements(): array
    {
        return $this->requirementRepository->getRequirements();
    }

    public function getUserRequirement(int $id): ?UserRequirement
    {
        return $this->requirementRepository->getUserRequirement($id);
    }

    public function getUserRequirements(int $userId): array
    {
        return $this->requirementRepository->getUserRequirements($userId);
    }

    public function createRequirement(CreateRequirementDTO $requirementDTO): void
    {
        $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();
        $requirement = $this->requirementRepository
            ->createRequirement($requirementDTO, $internshipCycleId);
        $this->createUserRequirements($requirement);
    }

    private function createUserRequirements(Requirement $requirement): void
    {
        if ($requirement->getRequirementType() === RequirementType::ONE_TIME) {
            $this->createOneTimeUserRequirements($requirement);
        } else {
            $this->createRecurringUserRequirements($requirement);
        }
    }

    private function createOneTimeUserRequirements(Requirement $requirement): void
    {
        $internshipCycle = $this->requirementRepository->getInternshipCycle($requirement);

        $users = $this->requirementRepository->getStudentUsers($internshipCycle);
        foreach ($users as $user) {
            $this->requirementRepository->createUserRequirement($requirement, $user);
        }
    }

    private function createRecurringUserRequirements(Requirement $requirement): void
    {
        $repeatInterval = $requirement->getRepeatInterval();

        $dateIncrementBy = "";

        if ($repeatInterval === RequirementRepeatInterval::DAILY) {
            $dateIncrementBy = "P1D";
        } else if ($repeatInterval === RequirementRepeatInterval::WEEKLY) {
            $dateIncrementBy = "P1W";
        } else if ($repeatInterval === RequirementRepeatInterval::MONTHLY) {
            $dateIncrementBy = "P1M";
        }

        $iterationDate = $requirement->getStartDate();

        $urDTOs = [];

        while ($iterationDate < $requirement->getEndBeforeDate()) {
            $urDTOs[] = new CreateUserRequirementDTO(
                $iterationDate,
                $iterationDate->add(new DateInterval($dateIncrementBy)),
            );
        }

        $internshipCycle = $this->requirementRepository->getInternshipCycle($requirement);
        $users = $this->requirementRepository->getStudentUsers($internshipCycle);

        foreach ($users as $user) {
            foreach ($urDTOs as $urDTO) {
                $this->requirementRepository
                    ->createUserRequirementFromDTO($requirement, $user, $urDTO);
            }
        }
    }

    public function completeUserRequirement(UserRequirementCompletionDTO $urCompletionDTO): void
    {
        $response = $this->fileStorageService->upload($urCompletionDTO->files);
        $ur = $this->getUserRequirement($urCompletionDTO->requirementId);

        $filePaths = [];

        foreach ($response["properties"] as $fileProperty) {
            $filePaths[] = $fileProperty["filePath"];
        }

        $ur->setFilePaths($filePaths);
        $ur->setCompletedAt(new DateTime("now"));
        $ur->setStatus("completed");
        $this->requirementRepository->saveUserRequirement($ur);
    }
}