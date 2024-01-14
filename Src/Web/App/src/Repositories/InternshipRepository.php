<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Internship;
use App\Mappers\InternshipMapper;
use Doctrine\ORM\EntityManager;
use PDO;

class InternshipRepository extends Repository
{
    public function __construct(
        private readonly PDO $pdo,
        EntityManager $entityManager
    ) {
        parent::__construct($entityManager);
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

    public function find(int $id): ?Internship
    {
        $query = $this->entityManager->createQuery(
            'SELECT i
            FROM App\Entities\Internship i
            WHERE i.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function findInternship(int $id): ?\App\Models\Internship
    {
        $stmt = $this->pdo->prepare('SELECT * FROM internships WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return InternshipMapper::map($result);
    }

    public function findAllBy(
        ?string $searchQuery,
        ?int $ownerId,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('i')
            ->from(Internship::class, 'i');

        if ($searchQuery) {
            $qb->where('i.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }
        if ($ownerId) {
            $qb->andWhere('i.owner = :ownerId')
                ->setParameter('ownerId', $ownerId);
        }
        if ($orderBy) {
            $qb->orderBy('i.' . $orderBy['column'], $orderBy['direction']);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * Summary of findOrganizations
     * @param array $ids Array of int (Organization IDs)
     * @return array Array of Organization
     */
    public function findOrganizations(array $ids): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entities\Organization o
            WHERE o.id IN (:ids)'
        )->setParameter('ids', $ids);

        return $query->getResult();
    }

    public function count(?string $searchQuery, ?int $ownerId, ): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(i)')
            ->from(Internship::class, 'i');
        if ($searchQuery) {
            $qb->where('i.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }
        if ($ownerId) {
            $qb->andWhere('i.owner = :ownerId')
                ->setParameter('ownerId', $ownerId);
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function apply(int $internshipId, int $studentUserId): bool
    {
        $sql = 'INSERT INTO internship_applicants (internship_id, student_id)
                VALUES (:internshipId, :studentUserId)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'internshipId' => $internshipId,
            'studentUserId' => $studentUserId,
        ]);
    }

    public function undoApply(int $internshipId, int $studentUserId): bool
    {
        $sql = 'DELETE FROM internship_applicants WHERE internship_id = :internshipId AND student_id = :studentUserId';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'internshipId' => $internshipId,
            'studentUserId' => $studentUserId,
        ]);
    }

    public function hasApplied(int $internshipId, int $studentUserId): bool
    {
        $sql = 'SELECT COUNT(*) FROM internship_applicants WHERE internship_id = :internshipId AND student_id = :studentUserId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'internshipId' => $internshipId,
            'studentUserId' => $studentUserId,
        ]);
        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result > 0;
    }

    public function delete(int $id): void
    {
        $query = $this->entityManager->createQuery(
            'DELETE FROM App\Entities\Internship i
            WHERE i.id = :id'
        )->setParameter('id', $id);

        $query->execute();
    }

    public function save(Internship $internship): void
    {
        $this->entityManager->persist($internship);
        $this->entityManager->flush();
    }

    public function createInternship(
        string $title,
        string $description,
        int $ownerId,
        int $organizationId,
        int $internshipCycleId,
        bool $isPublished,
    ): \App\Models\Internship {
        $sql = 'INSERT INTO internships (title, description, owner_user_id, organization_id, internship_cycle_id, createdAt, isPublished)
                VALUES (:title, :description, :ownerId, :organizationId, :internshipCycleId, NOW(), :isPublished)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'ownerId' => $ownerId,
            'organizationId' => $organizationId,
            'internshipCycleId' => $internshipCycleId,
            'isPublished' => $isPublished,
        ]);
        $internshipId = (int) $this->pdo->lastInsertId();
        return $this->findInternship($internshipId);
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