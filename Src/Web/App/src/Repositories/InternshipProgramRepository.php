<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IRepository;
use App\Mappers\InternshipCycleMapper;
use App\Models\InternshipCycle;
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

    public function findParticipants(int $cycleId, int $limit, int $offsetBy): array
    {
        $sql = 'SELECT u.id, u.firstName, u.email, u.type, s.fullName, s.studentEmail
                FROM users u
                INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
                LEFT JOIN students s ON u.id = s.id 
                LEFT JOIN partners p ON u.id = p.id
                WHERE ugm.usergroup_id IN (
                    SELECT ic.student_group_id
                    FROM internship_cycles ic
                    WHERE ic.id = :cycleId
                )
                GROUP BY u.id';
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        if ($offsetBy !== 0) {
            $sql .= " OFFSET :offsetBy";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue("limit", $limit, PDO::PARAM_INT);
        }
        if ($offsetBy !== 0) {
            $stmt->bindValue("offsetBy", $offsetBy, PDO::PARAM_INT);
        }
        $stmt->bindValue(':cycleId', $cycleId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return $data;
    }

    public function countParticipants(int $cycleId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users u
            INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
            WHERE ugm.usergroup_id IN (
                SELECT ic.student_group_id
                FROM internship_cycles ic
                WHERE ic.id = :cycleId
            )'
        );
        $stmt->bindValue(':cycleId', $cycleId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function findLatestCycle(): InternshipCycle
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
            throw new \Exception();
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

    public function createCycle(): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO internship_cycles
            (createdAt)
            VALUES
            (NOW())'
        );
        $stmt->execute();
        return (int) $this->pdo->lastInsertId();
    }

    public function endCycle(): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET endedAt = NOW()
            WHERE endedAt IS NULL'
        );
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function updateCycleUserGroups(
        int $id,
        array $partnerGroupIds,
        int $studentGroupId,
    ): bool {
        $statement = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET student_group_id = :stu_group_id
            WHERE id = :id'
        );
        $statement->execute([
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
     * @param array<string> $roles
     * @param array<int> $groupIds
     */
    public function removeRolesFromUserGroups(array $roles, array $groupIds): bool
    {
        $sql = "DELETE FROM user_group_roles
                WHERE usergroup_id IN (" . implode(',', $groupIds) . ")
                AND role_id IN (
                SELECT id FROM roles WHERE name IN ('" . implode("','", $roles) . "'))";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function modifyInternshipCycleDates(
        int $cycleId,
        ?DateTimeImmutable $jobCollectionStart,
        ?DateTimeImmutable $jobCollectionEnd,
        ?DateTimeImmutable $jobHuntRound1Start,
        ?DateTimeImmutable $jobHuntRound1End,
        ?DateTimeImmutable $jobHuntRound2Start,
        ?DateTimeImmutable $jobHuntRound2End,
    ): bool {
        $columns = '';
        $columns .= $jobCollectionStart ? "jobCollectionStart = '" . $jobCollectionStart->format($this::DATE_TIME_FORMAT) . "', " : '';
        $columns .= $jobCollectionEnd ? "jobCollectionEnd = '" . $jobCollectionEnd->format($this::DATE_TIME_FORMAT) . "', " : '';
        $columns .= $jobHuntRound1Start ? "jobHuntRound1Start = '" . $jobHuntRound1Start->format($this::DATE_TIME_FORMAT) . "', " : '';
        $columns .= $jobHuntRound1End ? "jobHuntRound1End = '" . $jobHuntRound1End->format($this::DATE_TIME_FORMAT) . "', " : '';
        $columns .= $jobHuntRound2Start ? "jobHuntRound2Start = '" . $jobHuntRound2Start->format($this::DATE_TIME_FORMAT) . "', " : '';
        $columns .= $jobHuntRound2End ? "jobHuntRound2End = '" . $jobHuntRound2End->format($this::DATE_TIME_FORMAT) . "', " : '';

        $commaPos = strrpos($columns, ',');
        if ($commaPos !== false) {
            $columns = substr_replace($columns, '', $commaPos, 1);
        }

        $sql = "UPDATE internship_cycles
                SET $columns
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function resetInternshipCycleDates(
        int $cycleId,
        bool $resetJobCollectionStart,
        bool $resetJobCollectionEnd,
        bool $resetJobHuntRound1Start,
        bool $resetJobHuntRound1End,
        bool $resetJobHuntRound2Start,
        bool $resetJobHuntRound2End,
    ): bool {
        $columns = '';
        $columns .= $resetJobCollectionStart ? "jobCollectionStart = NULL, " : '';
        $columns .= $resetJobCollectionEnd ? "jobCollectionEnd = NULL, " : '';
        $columns .= $resetJobHuntRound1Start ? "jobHuntRound1Start = NULL, " : '';
        $columns .= $resetJobHuntRound1End ? "jobHuntRound1End = NULL, " : '';
        $columns .= $resetJobHuntRound2Start ? "jobHuntRound2Start = NULL, " : '';
        $columns .= $resetJobHuntRound2End ? "jobHuntRound2End = NULL, " : '';

        $commaPos = strrpos($columns, ',');
        if ($commaPos !== false) {
            $columns = substr_replace($columns, '', $commaPos, 1);
        }

        $sql = "UPDATE internship_cycles
                SET $columns
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }
}