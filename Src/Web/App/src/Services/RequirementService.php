<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Entities\Requirement;
use App\Interfaces\IFileStorageService;
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
        private RequirementRepository $requirementRepository,
        private InternshipProgramService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    public function getRequirement(int $id): ?\App\Models\Requirement
    {
        return $this->requirementRepository->findRequirement($id);
    }

    /**
     * @return array<\App\Models\Requirement>
     */
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

    /**
     * @return array<UserRequirement>
     */
    public function getUserRequirements(
        int $cycleId,
        ?int $requirementId = null,
        ?int $userId = null,
        ?Status $status = null
    ): array {
        return $this->requirementRepository->findAllUserRequirements($cycleId, $requirementId, $userId, $status);
    }

    public function createRequirement(CreateRequirementDTO $reqDTO): void
    {
        $cycleId = $this->internshipCycleService->getLatestInternshipCycleId();
        $reqId = $this->requirementRepository
            ->createRequirement($cycleId, $reqDTO);
        // $this->createUserRequirements($req);
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

    public function completeUserRequirement(UserRequirementFulfillmentDTO $dto): bool
    {
        $ur = $this->requirementRepository->findUserRequirement($dto->userRequirementId);
        if (!$ur) {
            throw new Exception("User requirement not found");
        }

        if ($ur->getFulfillMethod() === FulFillMethod::FILE_UPLOAD) {
            $files = $this->fileStorageService->upload($dto->files);

            if ($files) {
                return $this->requirementRepository
                    ->fulfillUserRequirement($dto->userRequirementId, $files);
            }

            // TODO: Handle file upload failure
            return false;
        }

        return $this->requirementRepository
            ->fulfillUserRequirement($dto->userRequirementId, textResponse: $dto->textResponse);
    }
}