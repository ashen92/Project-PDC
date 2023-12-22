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

    public function findAllBy(array $criteria): array
    {
        return $this->entityManager->getRepository(Internship::class)->findBy($criteria);
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