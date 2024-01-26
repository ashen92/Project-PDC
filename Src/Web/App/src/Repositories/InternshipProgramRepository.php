<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IRepository;
use App\Mappers\InternshipCycleMapper;
use App\Mappers\StudentMapper;
use App\Models\InternshipCycle;
use App\Security\Role;
use DateTimeImmutable;
use PDO;

class InternshipProgramRepository implements IRepository
{
    public function __construct(
        private readonly PDO $pdo,
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

    public function findLatestCycle(): ?InternshipCycle
    {
        $sql = "SELECT ic.*, GROUP_CONCAT(icpg.usergroup_id SEPARATOR ',') AS partner_group_ids
                FROM internship_cycles ic
                LEFT JOIN internship_cycle_partner_groups icpg ON ic.id = icpg.internship_cycle_id
                WHERE endedAt IS NULL OR
                ic.endedAt = (SELECT MAX(ic.endedAt) FROM internship_cycles ic)
                GROUP BY ic.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return InternshipCycleMapper::map($result);
    }

    public function findLatestActiveCycle(): ?InternshipCycle
    {
        $sql = "SELECT ic.*, GROUP_CONCAT(icpg.usergroup_id SEPARATOR ',') AS partner_group_ids
                FROM internship_cycles ic
                LEFT JOIN internship_cycle_partner_groups icpg ON ic.id = icpg.internship_cycle_id
                WHERE endedAt IS NULL
                GROUP BY ic.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return InternshipCycleMapper::map($result);
    }

    public function findStudents(int $cycleId): array
    {
        $statement = $this->pdo->prepare("
            SELECT * FROM users
            INNER JOIN students
            ON users.id = students.id
            INNER JOIN user_group_membership
            ON users.id = user_group_membership.user_id
            WHERE user_group_membership.usergroup_id = (
                SELECT student_group_id FROM internship_cycles WHERE id = :internship_cycle_id
            )");
        $statement->execute([
            ":internship_cycle_id" => $cycleId,
        ]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($result === false) {
            return [];
        }

        return array_map(function ($row) {
            return StudentMapper::map($row);
        }, $result);
    }

    public function createCycle(): int
    {
        $statement = $this->pdo->prepare("
            INSERT INTO internship_cycles
            (createdAt)
            VALUES
            (NOW())
        ");
        $statement->execute();
        return (int) $this->pdo->lastInsertId();
    }

    public function endCycle(): bool
    {
        $statement = $this->pdo->prepare('
            UPDATE internship_cycles
            SET endedAt = NOW()
            WHERE endedAt IS NULL
        ');
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function updateCycle(
        int $id,
        DateTimeImmutable $collectionStartDate,
        DateTimeImmutable $collectionEndDate,
        DateTimeImmutable $applicationStartDate,
        DateTimeImmutable $applicationEndDate,
        array $partnerGroupIds,
        int $studentGroupId,
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET
                collectionStartDate = :col_start_date,
                collectionEndDate = :col_end_date,
                applicationStartDate = :app_start_date,
                applicationEndDate = :app_end_date,
                student_group_id = :stu_group_id
            WHERE id = :id'
        );
        $statement->execute([
            ':col_start_date' => $collectionStartDate->format(self::DATE_TIME_FORMAT),
            ':col_end_date' => $collectionEndDate->format(self::DATE_TIME_FORMAT),
            ':app_start_date' => $applicationStartDate->format(self::DATE_TIME_FORMAT),
            ':app_end_date' => $applicationEndDate->format(self::DATE_TIME_FORMAT),
            ':stu_group_id' => $studentGroupId,
            ':id' => $id,
        ]);

        $values = array_map(function ($i) use ($id) {
            return "($id, $i)";
        }, $partnerGroupIds);
        $valuesString = implode(', ', $values);

        $stmt = $this->pdo->prepare(
            "INSERT INTO internship_cycle_partner_groups
            (internship_cycle_id, usergroup_id)
            VALUES
            $valuesString"
        );
        return $stmt->execute();
    }

    /**
     * @param array<Role> $roles
     * @param array<int> $groupIds
     */
    public function removeRolesFromUserGroups(array $roles, array $groupIds): bool
    {
        $roles = array_map(fn($role) => $role->value, $roles);
        $sql = "DELETE FROM user_group_roles
                WHERE usergroup_id IN (" . implode(',', $groupIds) . ")
                AND role_id IN (
                SELECT id FROM roles WHERE name IN ('" . implode("','", $roles) . "'))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}