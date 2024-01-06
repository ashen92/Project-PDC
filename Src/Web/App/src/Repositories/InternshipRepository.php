<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Internship;

class InternshipRepository extends Repository
{
    public function find(int $id): ?Internship
    {
        $query = $this->entityManager->createQuery(
            'SELECT i
            FROM App\Entities\Internship i
            WHERE i.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
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

    public function hasApplied(int $internshipId, int $userId): bool
    {
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(i)
            FROM App\Entities\Internship i
            WHERE i = :internshipId
            AND :userId MEMBER OF i.applicants'
        )->setParameters([
                    'internshipId' => $internshipId,
                    'userId' => $userId
                ]);

        return $query->getSingleScalarResult() > 0;
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
}