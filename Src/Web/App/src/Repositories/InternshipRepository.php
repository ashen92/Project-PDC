<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Internship;
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
            ->select("i.id, i.title, i.description, u.firstName")
            ->leftJoin("i.user", "u");
        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getInternshipById(int $id): Internship|null
    {
        $queryBuilder = $this->createQueryBuilder("i");
        $queryBuilder
            ->select("i")
            ->where("i.id = :id")
            ->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}