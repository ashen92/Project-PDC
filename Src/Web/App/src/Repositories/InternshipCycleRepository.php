<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\InternshipCycle;

class InternshipCycleRepository extends Repository
{
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->entityManager
            ->getRepository(InternshipCycle::class)
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function save(InternshipCycle $internshipCycle): void
    {
        $this->entityManager->persist($internshipCycle);
        $this->entityManager->flush();
    }
}