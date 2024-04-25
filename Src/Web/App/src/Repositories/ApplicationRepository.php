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

    public function findApplication(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
            FROM applications
            WHERE id = :id"
        );
        $stmt->execute([
            "id" => $id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createIntern(
        int $studentId,
        int $partnerUserId,
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
                "adderUserId" => $partnerUserId,
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
            "adderUserId" => $partnerUserId,
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

    public function findAllApplicationsByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.id AS application_id, a.status AS application_status,
            i.title AS internship_title,
            o.name AS organization_name,
            JSON_ARRAYAGG(af.id) AS fileIds
            FROM applications a
            LEFT JOIN internships i ON a.internship_id = i.id
            LEFT JOIN organizations o ON i.organization_id = o.id
            LEFT JOIN application_files af ON a.id = af.application_id
            WHERE a.user_id = :studentId
            GROUP BY a.id"
        );
        $stmt->execute([
            "studentId" => $studentId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findApplicationFile(int $applicationId, int $fileId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT name, path
            FROM application_files
            WHERE application_id = :applicationId
            AND id = :fileId"
        );
        $stmt->execute([
            "applicationId" => $applicationId,
            "fileId" => $fileId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}