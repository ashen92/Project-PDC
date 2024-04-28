<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IRepository;
use App\Models\Application;
use DateTimeImmutable;
use PDO;

readonly class ApplicationRepository implements IRepository
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

    /**
     * @param array<array<string, string>> $files
     */
    public function createApplication(
        int $userId,
        array $files,
        ?int $internshipId,
        ?int $jobRoleId
    ): bool {
        $this->pdo->beginTransaction();
        try {
            $sql = 'INSERT INTO applications (user_id, internship_id, jobRoleId, status)
                VALUES (:userId, :internshipId, :jobRoleId, :status)';
            $stmt = $this->pdo->prepare($sql);
            if (
                !$stmt->execute([
                    'userId' => $userId,
                    'status' => 'pending',
                    'internshipId' => $internshipId,
                    'jobRoleId' => $jobRoleId,
                ])
            ) {
                throw new \Exception('Failed to create application');
            }

            $applicationId = $this->pdo->lastInsertId();

            $sql = 'INSERT INTO application_files (application_id, name, path) VALUES ';
            $sql .= implode(
                ',',
                array_map(
                    fn($file) => "($applicationId, '{$file['name']}', '{$file['path']}')",
                    $files
                )
            );
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute()) {
                throw new \Exception('Failed to create application files');
            }

            return $this->pdo->commit();
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function deleteApplication(int $userId, ?int $applicationId, ?int $internshipId, ?int $jobRoleId): bool
    {
        if ($internshipId === null && $jobRoleId === null)
            throw new \BadMethodCallException('Internship ID or Job Role ID must be provided');

        if ($internshipId !== null && $jobRoleId !== null)
            throw new \BadMethodCallException('Only one of Internship ID or Job Role ID must be provided');

        $this->pdo->beginTransaction();
        try {
            if ($applicationId === null) {
                $sql = 'SELECT id FROM applications WHERE user_id = :userId AND jobRoleId = :jobRoleId';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'userId' => $userId,
                    'jobRoleId' => $jobRoleId,
                ]);
                $applicationId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            }

            $sql = 'DELETE FROM application_files WHERE application_id = :applicationId';
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute(['applicationId' => $applicationId])) {
                throw new \Exception('Failed to delete application files');
            }

            if ($internshipId) {
                $sql = 'DELETE FROM applications WHERE internship_id = :internshipId AND user_id = :userId';
                $params = [
                    'internshipId' => $internshipId,
                    'userId' => $userId,
                ];
            } else {
                $sql = 'DELETE FROM applications WHERE jobRoleId = :jobRoleId AND user_id = :userId';
                $params = [
                    'jobRoleId' => $jobRoleId,
                    'userId' => $userId,
                ];
            }

            $stmt = $this->pdo->prepare($sql);
            if (
                !$stmt->execute($params)
            ) {
                throw new \Exception('Failed to delete application');
            }

            return $this->pdo->commit();
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function findAllApplicationsByJobRole(int $jobRoleId): array
    {
        $sql = "SELECT a.id, a.status, 
                    JSON_OBJECT(
                        'id', u.id,
                        'firstName', u.firstName,
                        'lastName', u.lastName,
                        'email', u.email
                    ) AS user,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', af.id,
                            'name', af.name,
                            'path', af.path
                        )
                    ) AS files,
                    CASE
                        WHEN interns.student_id IS NOT NULL THEN 0
                        ELSE 1
                    END AS isApplicantAvailable
                FROM applications a
                INNER JOIN students s ON a.user_id = s.id
                INNER JOIN users u ON a.user_id = u.id
                LEFT JOIN interns ON a.user_id = interns.student_id
                LEFT JOIN application_files af ON a.id = af.application_id
                WHERE a.jobRoleId = :jobRoleId
                GROUP BY a.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['jobRoleId' => $jobRoleId]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as &$r) {
            $r['user'] = json_decode($r['user'], true);
            $r['files'] = json_decode($r['files'], true);
            $r["isApplicantAvailable"] = $r["isApplicantAvailable"] === 1;
        }
        return $res;
    }

    public function countInternshipApplicationsByStudent(int $cycleId, int $studentId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS count
            FROM applications a
            INNER JOIN internships i ON a.internship_id = i.id
            WHERE user_id = :studentId
            AND i.internship_cycle_id = :cycleId"
        );
        $stmt->execute([
            "studentId" => $studentId,
            "cycleId" => $cycleId
        ]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function countJobRoleApplicationsByStudent(int $cycleId, int $studentId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS count
            FROM applications a
            INNER JOIN job_roles jr ON a.jobRoleId = jr.id
            WHERE user_id = :studentId
            AND jr.internship_cycle_id = :cycleId"
        );
        $stmt->execute([
            "studentId" => $studentId,
            "cycleId" => $cycleId
        ]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function isIntern(int $cycleId, ?int $studentId, ?int $applicationId): bool
    {
        if ($studentId === null && $applicationId === null) {
            throw new \InvalidArgumentException("Student ID or Application ID must be provided");
        }

        if ($studentId !== null && $applicationId !== null) {
            throw new \InvalidArgumentException("Only one of Student ID or Application ID must be provided");
        }

        if ($studentId !== null) {
            $sql = "SELECT COUNT(*) AS count
            FROM interns
            WHERE student_id = :studentId
            AND internship_cycle_id = :cycleId";
            $params = [
                "studentId" => $studentId,
                "cycleId" => $cycleId
            ];
        } else {
            $sql = "SELECT COUNT(*) AS count
            FROM interns
            WHERE student_id = (SELECT user_id FROM applications WHERE id = :applicationId)
            AND internship_cycle_id = :cycleId";
            $params = [
                "applicationId" => $applicationId,
                "cycleId" => $cycleId
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    public function createIntern(
        int $cycleId,
        int $studentId,
        int $partnerUserId,
        ?int $organizationId,
        ?int $applicationId
    ): bool {

        if ($organizationId === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO interns 
                (student_id, adder_user_id, organization_id, createdAt, application_id, internship_cycle_id) 
                VALUES 
                (:studentId, :adderUserId, 
                (SELECT organization_id FROM partners WHERE id = :adderUserId), 
                :createdAt, :applicationId, :cycleId)"
            );

            $stmt->execute([
                "studentId" => $studentId,
                "adderUserId" => $partnerUserId,
                "createdAt" => (new DateTimeImmutable())->format($this::DATE_TIME_FORMAT),
                "applicationId" => $applicationId,
                "cycleId" => $cycleId,
            ]);
            return $stmt->rowCount() === 1;
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO interns 
            (student_id, adder_user_id, organization_id, createdAt, application_id, internship_cycle_id) 
            VALUES 
            (:studentId, :adderUserId, :organizationId, :createdAt, :applicationId, :cycleId)"
        );
        $stmt->execute([
            "studentId" => $studentId,
            "adderUserId" => $partnerUserId,
            "organizationId" => $organizationId,
            "createdAt" => (new DateTimeImmutable())->format($this::DATE_TIME_FORMAT),
            "applicationId" => $applicationId,
            "cycleId" => $cycleId,
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

    public function deleteIntern(int $studentId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM interns 
            WHERE student_id = :studentId"
        );
        $stmt->execute([
            "studentId" => $studentId
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

    public function findJobRolesAppliedTo(int $cycleId, int $studentId): array
    {
        $sql = 'SELECT jr.id, jr.name
                FROM job_roles jr
                INNER JOIN applications a ON jr.id = a.jobRoleId
                WHERE jr.internship_cycle_id = :cycleId AND a.user_id = :studentId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cycleId' => $cycleId, 'studentId' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}