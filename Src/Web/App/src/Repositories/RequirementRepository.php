<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateRequirementDTO;
use App\Interfaces\IRepository;
use App\Mappers\RequirementMapper;
use App\Mappers\UserRequirementMapper;
use App\Models\Requirement;
use App\Models\UserRequirement;
use App\Models\UserRequirement\Status;
use DateTime;
use DateTimeImmutable;
use PDO;

readonly class RequirementRepository implements IRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function findRequirement(int $id): ?Requirement
    {
        $sql = "SELECT * FROM requirements WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id" => $id
        ]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res === false) {
            return null;
        }

        return RequirementMapper::map($res);
    }

    /**
     * @return array<Requirement>
     */
    public function findAllRequirements(int $cycleId): array
    {
        $sql = "SELECT * FROM requirements WHERE internship_cycle_id = :cycleId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "cycleId" => $cycleId
        ]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($res === false) {
            return [];
        }

        return array_map(function ($r) {
            return RequirementMapper::map($r);
        }, $res);
    }

    public function findUserRequirement(int $id): ?UserRequirement
    {
        $sql = "SELECT * FROM user_requirements WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "id" => $id
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }

        return UserRequirementMapper::map($result);
    }

    public function findAllUserRequirements(
        int $cycleId,
        ?int $requirementId = null,
        ?int $userId = null,
        ?Status $status = null
    ): array {
        $sql = "SELECT ur.*, r.internship_cycle_id FROM user_requirements ur
        INNER JOIN requirements r ON r.id = ur.requirement_id
        WHERE r.internship_cycle_id = :cycleId";
        $params = [
            "cycleId" => $cycleId
        ];

        if ($requirementId) {
            $sql .= " AND ur.requirement_id = :requirementId";
            $params["requirementId"] = $requirementId;
        }

        if ($userId) {
            $sql .= " AND ur.user_id = :userId";
            $params["userId"] = $userId;
        }

        if ($status) {
            $sql .= " AND ur.status = :status";
            $params["status"] = $status->value;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($results === false) {
            return [];
        }

        return array_map(function ($result) {
            return UserRequirementMapper::map($result);
        }, $results);
    }

    public function findUserRequirementsToBeCompleted(int $cycleId, int $userId): array
    {
        $sql = "SELECT ur.*, JSON_OBJECT(
                    'id', r.id,
                    'name', r.name,
                    'fulfillMethod', r.fulfillMethod
                ) AS requirement
                FROM user_requirements ur
                INNER JOIN requirements r ON r.id = ur.requirement_id
                WHERE r.internship_cycle_id = :cycleId
                AND ur.user_id = :userId
                AND ur.startDate <= :today
                AND ur.endDate >= :today
                GROUP BY ur.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "cycleId" => $cycleId,
            "userId" => $userId,
            "today" => (new DateTime())->format($this::DATE_TIME_FORMAT)
        ]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false) {
            return [];
        }

        foreach ($res as &$r) {
            $r['startDate'] = new DateTime($r['startDate']);
            $r['endDate'] = new DateTime($r['endDate']);
            $r['requirement'] = json_decode($r['requirement'], true);
        }
        return $res;
    }

    public function createRequirement(int $cycleId, CreateRequirementDTO $reqDTO): int
    {
        $sql = "INSERT INTO requirements (
            internship_cycle_id,
            name,
            description,
            startWeek,
            durationWeeks,
            fulfillMethod,
            allowedFileTypes,
            maxFileSize,
            maxFileCount
        ) VALUES (
            :cycleId,
            :name,
            :description,
            :startWeek,
            :durationWeeks,
            :fulfillMethod,
            :allowedFileTypes,
            :maxFileSize,
            :maxFileCount
        )";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "cycleId" => $cycleId,
            "name" => $reqDTO->name,
            "description" => $reqDTO->description,
            "startWeek" => $reqDTO->startWeek->format('%d days'),
            "durationWeeks" => $reqDTO->durationWeeks->format('%d days'),
            "fulfillMethod" => $reqDTO->fulfillMethod->value,
            "allowedFileTypes" => json_encode($reqDTO->allowedFileTypes),
            "maxFileSize" => $reqDTO->maxFileSize,
            "maxFileCount" => $reqDTO->maxFileCount
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function createUserRequirement(
        Requirement $requirement,
        int $userId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): bool {
        $sql = "INSERT INTO user_requirements (
                    user_id,
                    requirement_id,
                    status,
                    fulfillMethod,
                    startDate,
                    endDate
                )
                VALUES(:userId, :reqId, :status, :fulfillMethod, :startDate, :endDate)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "reqId" => $requirement->getId(),
            "userId" => $userId,
            "status" => Status::PENDING->value,
            "fulfillMethod" => $requirement->getFulfillMethod(),
            "startDate" => $startDate->format($this::DATE_TIME_FORMAT),
            "endDate" => $endDate->format($this::DATE_TIME_FORMAT)
        ]);
    }

    public function deleteUserRequirements(int $userId): bool
    {
        $sql = "DELETE FROM user_requirements WHERE user_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "userId" => $userId
        ]);
    }

    public function fulfillUserRequirement(int $userRequirementId, ?array $files = null, ?string $textResponse = null): bool
    {
        if ($files) {
            $this->pdo->beginTransaction();
            try {
                $sql = "UPDATE user_requirements SET
                    status = :status,
                    completedAt = :completedAt
                    WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                if (
                    !$stmt->execute([
                        "status" => Status::FULFILLED->value,
                        "completedAt" => (new DateTime())->format($this::DATE_TIME_FORMAT),
                        "id" => $userRequirementId,
                    ])
                ) {
                    return false;
                }

                $sql = "INSERT INTO user_requirement_files (user_requirement_id, name, path) VALUES ";
                $sql .= implode(
                    ',',
                    array_map(
                        fn($file) => "($userRequirementId, '{$file['name']}', '{$file['path']}')",
                        $files
                    )
                );
                $stmt = $this->pdo->prepare($sql);
                if (!$stmt->execute()) {
                    return false;
                }

                return $this->pdo->commit();
            } catch (\Throwable $th) {
                $this->pdo->rollBack();
                return false;
            }
        }

        $sql = "UPDATE user_requirements SET
                    status = :status,
                    completedAt = :completedAt,
                    textResponse = :textResponse
                    WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "status" => Status::FULFILLED->value,
            "completedAt" => (new DateTime())->format(self::DATE_TIME_FORMAT),
            "textResponse" => $textResponse,
            "id" => $userRequirementId,
        ]);
    }
}