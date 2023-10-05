<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findUserByEmail(string $email): User|null
    {
        $query = $this->createQueryBuilder("u")->where("u.email = :email")->setParameter(":email", $email)->getQuery();
        return $query->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        $queryBuilder = $this->createQueryBuilder("u");
        $queryBuilder
            ->select("r.name")
            ->innerJoin("u.groups", "g")
            ->innerJoin("g.roles", "r")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);

        return $queryBuilder->getQuery()->getSingleColumnResult();
    }
}