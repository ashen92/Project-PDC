<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\User;

class UserRepository extends Repository
{
    public function getUserRoles(int $userId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.name")
            ->from(User::class, "u")
            ->innerJoin("u.groups", "g")
            ->innerJoin("g.roles", "r")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);

        return $queryBuilder->getQuery()->getSingleColumnResult();
    }

    public function getUserById(int $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
    }

    public function getUserByStudentEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["studentEmail" => $email]);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["activationToken" => $token]);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}