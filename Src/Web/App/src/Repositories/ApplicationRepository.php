<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Application;
use PDO;

readonly class ApplicationRepository
{
    public function __construct(
        private PDO $pdo,
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

    public function createIntern(
        int $studentId,
        int $adderUserId,
        ?int $organizationId,
        ?int $applicationId
    ): bool {

        if ($organizationId === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO interns 
                (student_id, adder_user_id, organization_id, application_id) 
                VALUES 
                (:studentId, :adderUserId, 
                (SELECT organization_id FROM partners WHERE id = :adderUserId), 
                :applicationId)"
            );

            $stmt->execute([
                "studentId" => $studentId,
                "adderUserId" => $adderUserId,
                "applicationId" => $applicationId
            ]);
            return $stmt->rowCount() === 1;
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO interns 
            (student_id, adder_user_id, organization_id, application_id) 
            VALUES 
            (:studentId, :adderUserId, :organizationId, :applicationId)"
        );
        $stmt->execute([
            "studentId" => $studentId,
            "adderUserId" => $adderUserId,
            "organizationId" => $organizationId,
            "applicationId" => $applicationId
        ]);
        return $stmt->rowCount() === 1;
    }

    public function updateApplicationStatus(int $id, Application\Status $status): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE applications 
            SET status = :status 
            WHERE id = :id"
        );
        $stmt->execute([
            "status" => $status->value,
            "id" => $id
        ]);
        return $stmt->rowCount() === 1;
    }

    public function deleteInternIfExists(int $studentId, int $applicationId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM interns 
            WHERE student_id = :studentId 
            AND application_id = :applicationId"
        );
        $stmt->execute([
            "studentId" => $studentId,
            "applicationId" => $applicationId
        ]);
        return $stmt->rowCount() === 1;
    }
}