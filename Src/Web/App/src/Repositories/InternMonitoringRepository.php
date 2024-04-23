<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IRepository;
use DateTimeImmutable;
use PDO;

class InternMonitoringRepository implements IRepository
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

    /**
     * @return array<array<string>>
     */
    public function findStudents(int $cycleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.id, s.indexNumber, s.registrationNumber, s.fullName, s.studentEmail 
            FROM users u
            INNER JOIN students s ON u.id = s.id
            INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
            WHERE ugm.usergroup_id = 
                (SELECT student_group_id FROM internship_cycles WHERE id = :cycleId)'
        );
        $stmt->execute([
            ':cycleId' => $cycleId,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserRequirements(int $cycleId, int $requirementId): array
    {
        $sql = "SELECT ur.*,
                    s.indexNumber,
                    s.fullName,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', urf.id,
                            'name', urf.name
                        )
                    ) as files
                FROM user_requirements ur
                INNER JOIN students s ON ur.user_id = s.id
                INNER JOIN requirements r ON ur.requirement_id = r.id
                LEFT JOIN user_requirement_files urf ON ur.id = urf.user_requirement_id
                WHERE r.internship_cycle_id = :cycleId
                    AND ur.requirement_id = :requirementId
                GROUP BY ur.id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':cycleId', $cycleId, PDO::PARAM_INT);
        $stmt->bindParam(':requirementId', $requirementId, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['files'] = json_decode($row['files'], true);
            $row['startDate'] = new DateTimeImmutable($row['startDate']);
            $row['endDate'] = new DateTimeImmutable($row['endDate']);
            $row['completedAt'] = $row['completedAt'] ? new DateTimeImmutable($row['completedAt']) : null;
        }
        return $rows;
    }

    public function findUserRequirementFile(int $userRequirementId, int $fileId): array|bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT name, path
            FROM user_requirement_files
            WHERE user_requirement_id = :userRequirementId
            AND id = :fileId"
        );
        $stmt->execute([
            "userRequirementId" => $userRequirementId,
            "fileId" => $fileId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countUserRequirements(int $cycleId, int $requirementId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(ur.id) as count
            FROM user_requirements ur
            INNER JOIN requirements r ON ur.requirement_id = r.id
            WHERE r.internship_cycle_id = :cycleId
                AND ur.requirement_id = :requirementId"
        );
        $stmt->execute([
            ':cycleId' => $cycleId,
            ':requirementId' => $requirementId,
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function findStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.id, s.indexNumber, s.registrationNumber, s.fullName, s.studentEmail 
            FROM students s
            WHERE s.id = :studentId'
        );
        $stmt->execute([
            ':studentId' => $studentId,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserRequirementsByUserId(int $cycleId, int $studentId): array|bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT ur.id,
                ur.user_id,
                ur.requirement_id,
                ur.startDate,
                ur.endDate,
                ur.completedAt,
                ur.status,
                JSON_OBJECT(
                    'id', r.id, 'name', r.name
                ) as requirement
            FROM user_requirements ur
            INNER JOIN requirements r ON ur.requirement_id = r.id
            WHERE r.internship_cycle_id = :cycleId
                AND ur.user_id = :studentId"
        );
        $stmt->execute([
            ':cycleId' => $cycleId,
            ':studentId' => $studentId,
        ]);

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($res === false) {
            return false;
        }

        foreach ($res as &$row) {
            $row['requirement'] = json_decode($row['requirement'], true);
            $row['startDate'] = new DateTimeImmutable($row['startDate']);
            $row['endDate'] = new DateTimeImmutable($row['endDate']);
            $row['completedAt'] = $row['completedAt'] ? new DateTimeImmutable($row['completedAt']) : null;
        }

        return $res;
    }

    public function isEmployed(int $studentId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
            FROM interns
            WHERE student_id = :studentId'
        );
        $stmt->execute(["studentId" => $studentId]);
        $data = $stmt->fetch(PDO::FETCH_COLUMN);
        return $data > 0;
    }
}