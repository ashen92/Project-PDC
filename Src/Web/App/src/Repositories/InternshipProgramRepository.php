<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repository\IRepository;
use App\Mappers\InternshipCycleMapper;
use App\Mappers\StudentMapper;
use App\Models\InternshipCycle;

class InternshipProgramRepository implements IRepository
{
    public function __construct(
        private readonly \PDO $pdo,
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

    public function findCycle(int $id): ?InternshipCycle
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM internship_cycles WHERE id = :id"
        );
        $statement->execute([
            ":id" => $id,
        ]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return InternshipCycleMapper::map($result);
    }

    public function findLatestCycle(): ?InternshipCycle
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM internship_cycles ORDER BY createdAt DESC LIMIT 1"
        );
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
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
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return [];
        }

        return array_map(function ($row) {
            return StudentMapper::map($row);
        }, $result);
    }

    public function createCycle(): InternshipCycle
    {
        $statement = $this->pdo->prepare("
            INSERT INTO internship_cycles
            (createdAt)
            VALUES
            (NOW())
        ");
        $statement->execute();
        $id = $this->pdo->lastInsertId();
        return $this->findCycle((int) $id);
    }

    public function updateCycle(InternshipCycle $cycle): void
    {
        $statement = $this->pdo->prepare("
            UPDATE internship_cycles
            SET endedAt = :ended_at,
                collectionStartDate = :collection_start_date,
                collectionEndDate = :collection_end_date,
                applicationStartDate = :application_start_date,
                applicationEndDate = :application_end_date,
                student_group_id = :student_group_id,
                partner_group_id = :partner_group_id
            WHERE id = :id
        ");
        $statement->execute([
            ":ended_at" => $cycle->getEndedAt()?->format(self::DATE_TIME_FORMAT),
            ":collection_start_date" => $cycle->getCollectionStartDate()?->format(self::DATE_TIME_FORMAT),
            ":collection_end_date" => $cycle->getCollectionEndDate()?->format(self::DATE_TIME_FORMAT),
            ":application_start_date" => $cycle->getApplicationStartDate()?->format(self::DATE_TIME_FORMAT),
            ":application_end_date" => $cycle->getApplicationEndDate()?->format(self::DATE_TIME_FORMAT),
            ":student_group_id" => $cycle->getStudentGroupId(),
            ":partner_group_id" => $cycle->getPartnerGroupId(),
            ":id" => $cycle->getId(),
        ]);
    }
}