<?php
declare(strict_types=1);

namespace App\Repositories;

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
        $stmt = $this->pdo
            ->prepare('SELECT s.id, s.indexNumber, s.registrationNumber, s.fullName, s.studentEmail 
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
}