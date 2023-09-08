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

    public function saveUser(User $user)
    {
        // Save the user object to the database
    }
}