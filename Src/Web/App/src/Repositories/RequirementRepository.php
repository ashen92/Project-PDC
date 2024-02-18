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

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function findRequirement(int $id): ?Requirement
    {
        $sql = "SELECT * FROM requirements WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "id" => $id
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
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
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($results === false) {
            return [];
        }

        return array_map(function ($result) {
            return RequirementMapper::map($result);
        }, $results);
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

    /**
     * @return array</App/Models/UserRequirement>
     */
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

    public function createRequirement(int $cycleId, CreateRequirementDTO $reqDTO): int
    {
        $sql = "INSERT INTO requirements (
            internship_cycle_id,
            name,
            description,
            requirementType,
            startDate,
            endBeforeDate,
            repeatInterval,
            fulfillMethod,
            allowedFileTypes,
            maxFileSize,
            maxFileCount
        ) VALUES (
            :cycleId,
            :name,
            :description,
            :requirementType,
            :startDate,
            :endBeforeDate,
            :repeatInterval,
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
            "requirementType" => $reqDTO->requirementType->value,
            "startDate" => $reqDTO->startDate->format(self::DATE_TIME_FORMAT),
            "endBeforeDate" => $reqDTO->endBeforeDate?->format(self::DATE_TIME_FORMAT),
            "repeatInterval" => $reqDTO->repeatInterval->value,
            "fulfillMethod" => $reqDTO->fulfillMethod->value,
            "allowedFileTypes" => json_encode($reqDTO->allowedFileTypes),
            "maxFileSize" => $reqDTO->maxFileSize,
            "maxFileCount" => $reqDTO->maxFileCount
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function createOneTimeUserRequirements(int $reqId, ?int $userGroupId = null, ?int $userId = null): bool
    {
        if ($userId) {
            $sql = "INSERT INTO user_requirements (
                    user_id,
                    requirement_id,
                    status,
                    fulfillMethod,
                    startDate,
                    endDate
                )
                SELECT :userId, :reqId, :status, r.fulfillMethod, r.startDate, r.endBeforeDate
                FROM requirements r
                WHERE r.id = :reqId";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                "reqId" => $reqId,
                "userId" => $userId,
                "status" => Status::PENDING->value
            ]);
        }

        $sql = "INSERT INTO user_requirements (
                    user_id,
                    requirement_id,
                    status,
                    fulfillMethod,
                    startDate,
                    endDate
                )
                SELECT ugm.user_id, :reqId, :status, r.fulfillMethod, r.startDate, r.endBeforeDate
                FROM (
                    SELECT fulfillMethod, startDate, endBeforeDate
                    FROM requirements
                    WHERE id = :reqId
                ) AS r
                INNER JOIN user_group_membership ugm ON 1=1
                WHERE ugm.usergroup_id = :userGroupId";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "reqId" => $reqId,
            "userGroupId" => $userGroupId,
            "status" => Status::PENDING->value
        ]);
    }

    public function fulfillUserRequirement(int $id, ?array $filePaths = null, ?string $textResponse = null): bool
    {
        if ($filePaths) {
            $sql = "UPDATE user_requirements SET
                    status = :status,
                    completedAt = :completedAt
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            if(!$stmt->execute([
                "status" => Status::FULFILLED->value,
                "completedAt" => (new DateTime())->format(self::DATE_TIME_FORMAT),
                "id" => $id,
            ])) {
                return false;
            }

            $sql = "INSERT INTO files (name, path) VALUES (:name, :path)";
            $stmt = $this->pdo->prepare($sql);

            $fileIds = [];
            foreach ($filePaths as $file) {
                if (!$stmt->execute([
                    'name' => $file['name'],
                    'path' => $file['path'],
                ])) {
                    return false;
                }
                $fileIds[] = $this->pdo->lastInsertId();
            }

            $sql = "INSERT INTO user_requirement_files (user_requirement_id, file_id) VALUES ";
            $sql .= implode(", ", array_map(function ($fileId) use ($id) {
                return "($id, $fileId)";
            }, $fileIds));
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute();
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
            "id" => $id,
        ]);
    }
}