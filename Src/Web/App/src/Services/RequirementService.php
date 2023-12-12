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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class RequirementService implements IRequirementService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequirementRepository $requirementRepository,
        private InternshipCycleService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {

    }

    public function getRequirements(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.id, r.name, r.description, r.requirementType, r.startDate, r.endBeforeDate, r.repeatInterval")
            ->from("App\Entities\Requirement", "r");
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function getRequirement(int $id): ?Requirement
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\Requirement', 'i');

        $sql = "SELECT r.* FROM requirements r WHERE r.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        return $query->getOneOrNullResult();
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

    public function getUserRequirements(int $userId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("ur.id, r.id as r_id, r.name, r.description, r.requirementType, r.startDate, r.repeatInterval")
            ->from("App\Entities\UserRequirement", "ur")
            ->innerJoin("ur.requirement", "r")
            ->innerJoin("ur.user", "u")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function getUserRequirement(int $id): ?UserRequirement
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\UserRequirement', 'i');

        $sql = "SELECT ur.* 
                FROM user_requirements ur 
                INNER JOIN requirements r ON ur.requirement_id = r.id
                INNER JOIN users u ON ur.user_id = u.id
                WHERE ur.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        return $query->getOneOrNullResult();
    }

    public function completeUserRequirement(UserRequirementCompletionDTO $urCompletionDTO): void
    {
        $response = $this->fileStorageService->upload($urCompletionDTO->files);
        $ur = $this->entityManager
            ->getRepository(UserRequirement::class)
            ->find($urCompletionDTO->requirementId);

        $filePaths = [];

        foreach ($response["properties"] as $fileProperty) {
            $filePaths[] = $fileProperty["filePath"];
        }

        $ur->setFilePaths($filePaths);
        $ur->setCompletedAt(new DateTime("now"));
        $ur->setStatus("completed");
        $this->entityManager->flush();
    }
}