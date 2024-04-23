<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IRepository;
use App\Mappers\InternshipMapper;
use App\Mappers\InternshipSearchResultMapper;
use App\Mappers\OrganizationMapper;
use App\Models\Internship;
use App\Models\Internship\Visibility;
use App\Models\InternshipSearchResult;
use App\Models\Organization;
use PDO;

class InternshipRepository implements IRepository
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

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function findInternship(int $id): ?Internship
    {
        $stmt = $this->pdo->prepare('SELECT * FROM internships WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return InternshipMapper::map($result);
    }

    /**
     * @return array<Internship>
     */
    public function findInternships(int $cycleId, int $ownerId): array
    {
        $sql = 'SELECT * FROM internships WHERE internship_cycle_id = :cycleId AND created_by_user_id = :ownerId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'cycleId' => $cycleId,
            'ownerId' => $ownerId
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $result) => InternshipMapper::map($result), $results);
    }

    /**
     * @return array<InternshipSearchResult>
     */
    public function searchInternships(
        ?int $cycleId,
        ?string $searchQuery,
        ?array $filterByOrgIds,
        ?Internship\Visibility $filterByVisibility,
        ?bool $isApproved,
        ?int $numberOfResults,
        ?int $offsetBy,
        ?int $filterByCreatorUserId,
    ): array {
        $sql = 'SELECT i.*, o.name AS orgName, o.logoFilePath AS orgLogoFilePath
                FROM internships i
                JOIN organizations o ON i.organization_id = o.id';
        $params = [];
        if ($cycleId) {
            $sql .= ' AND i.internship_cycle_id = :cycleId';
            $params['cycleId'] = $cycleId;
        }
        if ($filterByOrgIds) {
            $val = implode(',', $filterByOrgIds);
            $sql .= " AND i.organization_id in ($val)";
        }
        if ($filterByVisibility) {
            $sql .= " AND i.visibility = :visibility";
            $params['visibility'] = $filterByVisibility->value;
        }
        if ($isApproved !== null) {
            if ($isApproved === true) {
                $sql .= ' AND i.isApproved = :isApproved';
            } else {
                $sql .= ' AND i.isApproved != :isApproved';
            }
            $params['isApproved'] = true;
        }
        if ($filterByCreatorUserId) {
            $sql .= ' AND i.created_by_user_id = :creatorUserId';
            $params['creatorUserId'] = $filterByCreatorUserId;
        }
        if ($searchQuery) {
            $sql .= ' AND i.title LIKE :searchQuery';
            $params['searchQuery'] = '%' . $searchQuery . '%';
        }
        if ($numberOfResults) {
            $sql .= ' ORDER BY i.createdAt DESC LIMIT ' . $numberOfResults;
        }
        if ($offsetBy) {
            $sql .= ' OFFSET ' . $offsetBy;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $result) => InternshipSearchResultMapper::map($result), $results);
    }

    public function getOrganizationsForSearchQuery(
        int $cycleId,
        ?string $searchQuery,
        ?Visibility $visibility,
        ?bool $isApproved
    ): array {
        $sql = 'SELECT DISTINCT o.*
                FROM internships i
                JOIN organizations o ON i.organization_id = o.id
                WHERE i.internship_cycle_id = :cycleId';
        $params = ['cycleId' => $cycleId];
        if ($searchQuery) {
            $sql .= ' AND i.title LIKE :searchQuery';
            $params['searchQuery'] = '%' . $searchQuery . '%';
        }
        if ($visibility) {
            $sql .= " AND i.visibility = :visibility";
            $params['visibility'] = $visibility->value;
        }
        if ($isApproved !== null) {
            if ($isApproved === true) {
                $sql .= ' AND i.isApproved = :isApproved';
            } else {
                $sql .= ' AND i.isApproved != :isApproved';
            }
            $params['isApproved'] = true;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $result) => OrganizationMapper::map($result), $results);
    }

    /**
     * @param array<int> $ids
     * @return array<Organization>
     */
    public function findOrganizations(): array
    {
        $sql = 'SELECT * FROM organizations';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn(array $result) => OrganizationMapper::map($result), $results);
    }

    public function count(
        int $cycleId,
        ?string $searchQuery,
        ?array $filterByOrgIds,
        ?Internship\Visibility $filterByVisibility,
        ?bool $isApproved,
        ?int $ownerUserId
    ): int {
        $sql = 'SELECT COUNT(*) FROM internships WHERE internship_cycle_id = :cycleId';
        $params = ['cycleId' => $cycleId];
        if ($searchQuery) {
            $sql .= ' AND title LIKE :searchQuery';
            $params['searchQuery'] = '%' . $searchQuery . '%';
        }
        if ($filterByOrgIds) {
            $val = implode(',', $filterByOrgIds);
            $sql .= " AND organization_id in ($val)";
        }
        if ($filterByVisibility) {
            $sql .= ' AND visibility = :visibility';
            $params['visibility'] = $filterByVisibility->value;
        }
        if ($isApproved !== null) {
            if ($isApproved === true) {
                $sql .= ' AND isApproved = :isApproved';
            } else {
                $sql .= ' AND isApproved != :isApproved';
            }
            $params['isApproved'] = true;
        }
        if ($ownerUserId) {
            $sql .= ' AND created_by_user_id = :ownerId';
            $params['ownerId'] = $ownerUserId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function findInternshipDetailsForStudent(int $internshipId, int $studentId): array
    {
        $sql = 'SELECT i.*, o.*, a.id as application_id
                FROM internships i
                LEFT JOIN organizations o ON i.organization_id = o.id
                LEFT JOIN applications a ON i.id = a.internship_id AND a.user_id = :studentId
                WHERE i.id = :internshipId';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'studentId' => $studentId,
            'internshipId' => $internshipId,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<mixed>
     */
    public function findAllApplications(int $internshipId): array
    {
        $sql = 'SELECT a.id, a.user_id AS userId, a.status, 
                    s.fullName AS studentFullName,
                    u.firstName AS userFirstName,
                    u.email AS userEmail,
                    CASE
                        WHEN interns.student_id IS NOT NULL THEN 0
                        ELSE 1
                    END AS isApplicantAvailable
                FROM applications a
                INNER JOIN students s ON a.user_id = s.id
                INNER JOIN users u ON a.user_id = u.id
                LEFT JOIN interns ON a.user_id = interns.student_id
                WHERE a.internship_id = :internshipId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['internshipId' => $internshipId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array<array<string, string>> $files
     */
    public function createApplication(int $internshipId, int $userId, array $files): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = 'INSERT INTO applications (internship_id, user_id, status)
                VALUES (:internshipId, :userId, :status)';
            $stmt = $this->pdo->prepare($sql);
            if (
                !$stmt->execute([
                    'internshipId' => $internshipId,
                    'userId' => $userId,
                    'status' => 'pending',
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

    public function deleteApplication(int $applicationId, int $internshipId, int $userId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = 'DELETE FROM application_files WHERE application_id = :applicationId';
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute(['applicationId' => $applicationId])) {
                throw new \Exception('Failed to delete application files');
            }

            $sql = 'DELETE FROM applications WHERE internship_id = :internshipId AND user_id = :userId';
            $stmt = $this->pdo->prepare($sql);
            if (
                !$stmt->execute([
                    'internshipId' => $internshipId,
                    'userId' => $userId,
                ])
            ) {
                throw new \Exception('Failed to delete application');
            }

            return $this->pdo->commit();
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function hasApplied(int $internshipId, int $userId): bool
    {
        $sql = 'SELECT COUNT(*) FROM applications WHERE internship_id = :internshipId AND user_id = :userId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'internshipId' => $internshipId,
            'userId' => $userId,
        ]);
        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result > 0;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM internships WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function createInternship(
        int $internshipCycleId,
        createInternshipDTO $dto,
    ): bool {
        if ($dto->organizationId === null) {
            $sql = 'INSERT INTO internships (
                title, description, 
                visibility, isApproved, internship_cycle_id,
                created_by_user_id, organization_id, createdAt)
            VALUES (
                :title, :description, 
                :visibility, :isApproved, :internshipCycleId, 
                :createdByUserId, 
                (SELECT organization_id FROM partners WHERE id = :createdByUserId),
                NOW())';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'title' => $dto->title,
                'description' => $dto->description,
                'visibility' => $dto->visibility->value,
                'isApproved' => $dto->isApproved ? 1 : 0,
                'internshipCycleId' => $internshipCycleId,
                'createdByUserId' => $dto->createdByUserId,
            ]);
        }
        $sql = 'INSERT INTO internships (
                    title, description, 
                    visibility, isApproved, internship_cycle_id,
                    created_by_user_id, organization_id, createdAt)
                VALUES (
                    :title, :description, 
                    :visibility, :isApproved, :internshipCycleId, 
                    :createdByUserId, :organizationId, NOW())';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $dto->title,
            'description' => $dto->description,
            'visibility' => $dto->visibility->value,
            'isApproved' => $dto->isApproved ? 1 : 0,
            'internshipCycleId' => $internshipCycleId,
            'createdByUserId' => $dto->createdByUserId,
            'organizationId' => $dto->organizationId,
        ]);
    }

    public function updateInternship(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?bool $isPublished = null
    ): bool {
        if ($title === null && $description === null && $isPublished === null) {
            return true;
        }

        $sql = 'UPDATE internships SET ';
        $params = [];
        if ($title !== null) {
            $sql .= 'title = :title';
            $params['title'] = $title;
        }
        if ($description !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'description = :description';
            $params['description'] = $description;
        }
        if ($isPublished !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'isPublished = :isPublished';
            $params['isPublished'] = $isPublished;
        }
        $sql .= ' WHERE id = :id';
        $params['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function findJobRole(int $id): array
    {
        $sql = 'SELECT * FROM job_roles WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findJobRoles(int $cycleId): array
    {
        $sql = 'SELECT jr.id, jr.name
                FROM job_roles jr
                WHERE jr.internship_cycle_id = :cycleId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cycleId' => $cycleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findJobRolesAppliedTo(int $cycleId, int $studentId): array
    {
        $sql = 'SELECT jr.id, jr.name
                FROM job_roles jr
                INNER JOIN job_role_students jrs ON jr.id = jrs.jobrole_id
                WHERE jr.internship_cycle_id = :cycleId AND jrs.student_id = :studentId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cycleId' => $cycleId, 'studentId' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findStudentsByJobRole(int $jobRoleId): array
    {
        $sql = 'SELECT u.*, s.*
                FROM users u
                INNER JOIN students s ON u.id = s.id
                INNER JOIN job_role_students jrs ON u.id = jrs.student_id
                WHERE jrs.jobrole_id = :jobRoleId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['jobRoleId' => $jobRoleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function applyToJobRole(int $jobRoleId, int $studentId): bool
    {
        $sql = 'INSERT INTO job_role_students (student_id, jobrole_id) VALUES (:studentId, :jobRoleId)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['studentId' => $studentId, 'jobRoleId' => $jobRoleId]);
    }

    public function removeFromJobRole(int $jobRoleId, int $studentId): bool
    {
        $sql = 'DELETE FROM job_role_students WHERE student_id = :studentId AND jobrole_id = :jobRoleId';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['studentId' => $studentId, 'jobRoleId' => $jobRoleId]);
    }

    public function createJobRole(int $cycleId, string $name): bool
    {
        $sql = 'INSERT INTO job_roles (internship_cycle_id, name) VALUES (:cycleId, :name)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['cycleId' => $cycleId, 'name' => $name]);
    }

    public function modifyJobRole(int $id, string $name): bool
    {
        $sql = 'UPDATE job_roles SET name = :name WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public function deleteJobRole(int $id): bool
    {
        $sql = 'DELETE FROM job_roles WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}