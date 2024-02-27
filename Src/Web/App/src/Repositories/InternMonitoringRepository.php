<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOMappers\UserRequirementTableViewDTOMapper;
use App\DTOs\UserRequirementTableViewDTO;
use App\Interfaces\IRepository;
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

    public function findUserRequirements(
        int $cycleId,
        int $requirementId,
        int $limit,
        int $offset
    ): array {
        $sql = "SELECT ur.*, 
                    DATE(ur.startDate) as startDate, 
                    DATE(ur.endDate) as endDate, 
                    s.indexNumber,
                    s.fullName,
                    GROUP_CONCAT(f.id, ':', f.name, ':', f.path SEPARATOR '|') as files
                FROM user_requirements ur
                INNER JOIN students s ON ur.user_id = s.id
                INNER JOIN requirements r ON ur.requirement_id = r.id
                LEFT JOIN user_requirement_files urf ON ur.id = urf.user_requirement_id
                LEFT JOIN files f ON urf.file_id = f.id
                WHERE r.internship_cycle_id = :cycleId
                    AND ur.requirement_id = :requirementId
                GROUP BY ur.id
                LIMIT :limit";

        if ($offset !== 0) {
            $sql .= " OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':cycleId', $cycleId, PDO::PARAM_INT);
        $stmt->bindParam(':requirementId', $requirementId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        if ($offset !== 0) {
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    /**
     * @return array<UserRequirementTableViewDTO>
     */
    public function getUserRequirementsByUserId(int $cycleId, int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ur.id,
                ur.user_id,
                ur.requirement_id,
                ur.startDate,
                ur.endDate,
                ur.completedAt,
                ur.status,
                r.name AS requirementName
            FROM user_requirements ur
            INNER JOIN requirements r ON ur.requirement_id = r.id
            WHERE r.internship_cycle_id = :cycleId
                AND ur.user_id = :studentId"
        );
        $stmt->execute([
            ':cycleId' => $cycleId,
            ':studentId' => $studentId,
        ]);
        return array_map(
            fn($row) => UserRequirementTableViewDTOMapper::map($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}