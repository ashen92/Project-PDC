<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IRepository;
use App\Mappers\InternshipCycleMapper;
use App\Models\InternshipCycle;
use App\Security\Role;
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

    public function startJobCollection(int $cycleId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET jobCollectionStart = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoStartJobCollection(int $cycleId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET jobCollectionStart = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function endJobCollection(int $cycleId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET jobCollectionEnd = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoEndJobCollection(int $cycleId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET jobCollectionEnd = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function startApplying(int $cycleId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET applyingStart = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoStartApplying(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET applyingStart = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function endApplying(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET applyingEnd = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoEndApplying(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET applyingEnd = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function startInterning(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET interningStart = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoStartInterning(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET interningStart = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function endInterning(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET interningEnd = NOW()
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }

    public function undoEndInterning(int $cycleId)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE internship_cycles
            SET interningEnd = null
            WHERE id = :id'
        );
        $stmt->execute([':id' => $cycleId]);
        return $stmt->rowCount() > 0;
    }
}