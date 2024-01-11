<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\Entities\InternshipCycle;
use App\Entities\Requirement;
use App\Entities\User;
use App\Entities\UserRequirement;
use App\Interfaces\Repository\IRepository;
use App\Mappers\RequirementMapper;
use App\Mappers\UserRequirementMapper;

class RequirementRepository extends Repository implements IRepository
{
    private const string DATE_TIME_FORMAT = "Y-m-d H:i:s";

    public function __construct(
        private readonly \PDO $pdo,
        \Doctrine\ORM\EntityManager $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function findRequirement(int $id): ?\App\Models\Requirement
    {
        $sql = "SELECT * FROM requirements WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "id" => $id
        ]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }

        return RequirementMapper::map($result);
    }

    public function findAllRequirements(int $cycleId): array
    {
        $sql = "SELECT * FROM requirements WHERE internship_cycle_id = :cycleId";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "cycleId" => $cycleId
        ]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if ($results === false) {
            return [];
        }

        return array_map(function ($result) {
            return RequirementMapper::map($result);
        }, $results);
    }

    public function findUserRequirement(int $id): ?\App\Models\UserRequirement
    {
        $sql = "SELECT * FROM user_requirements WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "id" => $id
        ]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }

        return UserRequirementMapper::map($result);
    }

    public function findAllUserRequirements(array $criteria): array
    {
        return $this->entityManager->getRepository(UserRequirement::class)->findBy($criteria);
    }

    public function createRequirement(
        CreateRequirementDTO $requirementDTO,
        int $internshipCycleId
    ): Requirement {
        $requirement = new Requirement($requirementDTO);
        $internshipCycle = $this->entityManager->find(
            InternshipCycle::class,
            $internshipCycleId
        );
        $requirement->setInternshipCycle($internshipCycle);
        $this->entityManager->persist($requirement);
        $this->entityManager->flush();
        return $requirement;
    }

    public function createUserRequirement(
        Requirement $requirement,
        User $user
    ): UserRequirement {
        $userRequirement = new UserRequirement($user, $requirement);
        $this->entityManager->persist($userRequirement);
        $this->entityManager->flush();
        return $userRequirement;
    }

    public function createUserRequirementFromDTO(
        Requirement $requirement,
        User $user,
        CreateUserRequirementDTO $userRequirementDTO
    ): UserRequirement {
        $userRequirement = new UserRequirement($user, $requirement);
        $userRequirement->setStartDate($userRequirementDTO->startDate);
        $userRequirement->setEndDate($userRequirementDTO->endDate);
        $userRequirement->setStatus($userRequirementDTO->status);
        $this->entityManager->persist($userRequirement);
        $this->entityManager->flush();
        return $userRequirement;
    }

    public function updateUserRequirement(\App\Models\UserRequirement $ur): void
    {
        $sql = "UPDATE user_requirements SET
            startDate = :startDate,
            endDate = :endDate,
            status = :status,
            completedAt = :completedAt,
            textResponse = :textResponse,
            filePaths = :filePaths
            WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "startDate" => $ur->getStartDate()->format(self::DATE_TIME_FORMAT),
            "endDate" => $ur->getEndDate()->format(self::DATE_TIME_FORMAT),
            "status" => $ur->getStatus(),
            "completedAt" => $ur->getCompletedAt() !== null
                ? $ur->getCompletedAt()->format(self::DATE_TIME_FORMAT)
                : null,
            "textResponse" => $ur->getTextResponse(),
            "filePaths" => $ur->getFilePaths() !== null
                ? json_encode($ur->getFilePaths())
                : null,
            "id" => $ur->getId(),
        ]);
    }
}