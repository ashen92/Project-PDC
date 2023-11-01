<?php
declare(strict_types=1);

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class InternshipRepository extends EntityRepository
{
    /**
     * @return array An array of internships
     */
    public function getInternships(): array
    {
        $queryBuilder = $this->createQueryBuilder("i");
        $queryBuilder
            ->select("i");
        return $queryBuilder->getQuery()->getArrayResult();
    }
}