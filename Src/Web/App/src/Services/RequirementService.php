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
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\Type;
use App\Repositories\RequirementRepository;
use DateInterval;
use DateTimeImmutable;

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
        return $this->requirementRepository->findRequirement($id);
    }

    public function getRequirements(?int $internshipCycleId = null): array
    {
        if ($internshipCycleId === null)
            $internshipCycleId = $this->internshipCycleService->getLatestInternshipCycleId();

        if ($internshipCycleId === null)
            return [];

        return $this->requirementRepository->findAllRequirements($internshipCycleId);
    }

    public function getUserRequirement(int $id): ?UserRequirement
    {
        return $this->requirementRepository->findUserRequirement($id);
    }

    public function getUserRequirements(
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

    public function createRequirement(CreateRequirementDTO $requirementDTO): void
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

    public function completeUserRequirement(UserRequirementCompletionDTO $urCompletionDTO): bool
    {
        $ur = $this->getUserRequirement($urCompletionDTO->userRequirementId);

        if (!$ur) {
            return false;
        }

        if ($ur->getRequirement()->getFulfillMethod() === FulFillMethod::FILE_UPLOAD) {
            $response = $this->fileStorageService->upload($urCompletionDTO->files);

            if ($response) {
                $filePaths = [];

                foreach ($response["properties"] as $fileProperty) {
                    $filePaths[] = $fileProperty["filePath"];
                }

                $ur->setFilePaths($filePaths);
            }
        }

        if ($ur->getRequirement()->getFulfillMethod() === FulFillMethod::TEXT_INPUT) {
            $ur->setTextResponse($urCompletionDTO->textResponse);
        }

        $ur->setCompletedAt(new DateTimeImmutable("now"));
        $ur->setStatus("completed");
        $this->requirementRepository->saveUserRequirement($ur);
        return true;
    }
}