<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Internship;

class InternshipRepository extends Repository
{
    public function findAll(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT i
            FROM App\Entities\Internship i
            ORDER BY i.id ASC'
        );

        return $query->getResult();
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

    public function findByOwner(int $userId): array
    {
        return $this->entityManager->getRepository(Internship::class)->findBy(["owner" => $userId]);
    }

    public function findByTitleAndOwner(string $title, ?string $ownerId = null): array
    {
        if ($ownerId !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select("i")
                ->from("App\Entities\Internship", "i")
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq("i.user", ":userId"),
                        $queryBuilder->expr()->like("LOWER(i.title)", ":searchQuery")
                    )
                )
                ->setParameter("userId", $ownerId)
                ->setParameter("searchQuery", "%{$title}%");
            $query = $queryBuilder->getQuery();
            return $query->getArrayResult();
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("i")
            ->from("App\Entities\Internship", "i")
            ->where("LOWER(i.title) LIKE :searchQuery")
            ->setParameter("searchQuery", "%{$title}%");
        $query = $queryBuilder->getQuery();
        return $query->getResult();
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