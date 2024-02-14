<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IRepository;
use App\Mappers\InternshipMapper;
use App\Mappers\InternshipSearchResultMapper;
use App\Mappers\OrganizationMapper;
use App\Models\Internship;
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
        ?int $ownerUserId,
        ?string $searchQuery,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array {
        $sql = 'SELECT i.*, o.name AS orgName, o.logoFilePath AS orgLogoFilePath
                FROM internships i
                JOIN organizations o ON i.organization_id = o.id';
        $params = [];
        if ($cycleId) {
            $sql .= ' AND i.internship_cycle_id = :cycleId';
            $params['cycleId'] = $cycleId;
        }
        if ($ownerUserId) {
            $sql .= ' AND i.created_by_user_id = :ownerUserId';
            $params['ownerUserId'] = $ownerUserId;
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

    public function searchInternshipsGetOrganizations(int $cycleId, ?string $searchQuery): array
    {
        $sql = 'SELECT DISTINCT o.*
                FROM internships i
                JOIN organizations o ON i.organization_id = o.id
                WHERE i.internship_cycle_id = :cycleId';
        $params = ['cycleId' => $cycleId];
        if ($searchQuery) {
            $sql .= ' AND i.title LIKE :searchQuery';
            $params['searchQuery'] = '%' . $searchQuery . '%';
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

    public function count(int $cycleId, ?string $searchQuery, ?int $ownerUserId): int
    {
        $sql = 'SELECT COUNT(*) FROM internships WHERE internship_cycle_id = :cycleId';
        $params = ['cycleId' => $cycleId];
        if ($searchQuery) {
            $sql .= ' AND title LIKE :searchQuery';
            $params['searchQuery'] = '%' . $searchQuery . '%';
        }
        if ($ownerUserId) {
            $sql .= ' AND created_by_user_id = :ownerId';
            $params['ownerId'] = $ownerUserId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * @return array<mixed>
     */
    public function findAllApplications(int $internshipId): array
    {
        $sql = 'SELECT a.id, a.user_id, a.status, 
                        s.fullName AS studentFullName, 
                        u.firstName AS userFirstName,
                        u.email AS userEmail
                FROM applications a
                INNER JOIN students s ON a.user_id = s.id
                INNER JOIN users u ON a.user_id = u.id
                WHERE a.internship_id = :internshipId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['internshipId' => $internshipId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apply(int $internshipId, int $userId): bool
    {
        $sql = 'INSERT INTO applications (internship_id, user_id, status)
                VALUES (:internshipId, :userId, :status)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'internshipId' => $internshipId,
            'userId' => $userId,
            'status' => 'pending',
        ]);
    }

    public function undoApply(int $internshipId, int $userId): bool
    {
        $sql = 'DELETE FROM applications WHERE internship_id = :internshipId AND user_id = :userId';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'internshipId' => $internshipId,
            'userId' => $userId,
        ]);
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
                status,internship_cycle_id,
                created_by_user_id, organization_id, createdAt)
            VALUES (
                :title, :description, 
                :status, :internshipCycleId, 
                :createdByUserId, 
                (SELECT organization_id FROM partners WHERE id = :createdByUserId),
                NOW())';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'title' => $dto->title,
                'description' => $dto->description,
                'status' => $dto->status->value,
                'internshipCycleId' => $internshipCycleId,
                'createdByUserId' => $dto->createdByUserId,
            ]);
        }
        $sql = 'INSERT INTO internships (
                    title, description, 
                    status,internship_cycle_id,
                    created_by_user_id, organization_id, createdAt)
                VALUES (
                    :title, :description, 
                    :status, :internshipCycleId, 
                    :createdByUserId, :organizationId, NOW())';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $dto->title,
            'description' => $dto->description,
            'status' => $dto->status->value,
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
}