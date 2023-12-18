<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Role;
use App\Entities\User;
use App\Entities\UserGroup;

class UserRepository extends Repository
{
    public function find(int $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

    public function findUserRoles(int $userId): array
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

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
    }

    public function findByStudentEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["studentEmail" => $email]);
    }

    public function findByActivationToken(string $token): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["activationToken" => $token]);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findUserGroup(int $groupId): ?UserGroup
    {
        return $this->entityManager->getRepository(UserGroup::class)->find($groupId);
    }

    public function findAllUserGroups(): array
    {
        return $this->entityManager->getRepository(UserGroup::class)->findAll();
    }

    public function findRoleByName(string $roleName): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy(["name" => $roleName]);
    }

    public function addUserGroup(string $groupName): UserGroup
    {
        $userGroup = new UserGroup($groupName);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
        return $userGroup;
    }

    public function addUsersToUserGroup(int $groupId, int $fromUserGroupId): void
    {
        $userGroup = $this->findUserGroup($groupId);
        $userGroup->addUsersFrom($this->findUserGroup($fromUserGroupId));
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
    }

    public function addRoleToUserGroup(int $groupId, string $roleName): void
    {
        $userGroup = $this->findUserGroup($groupId);
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(["name" => $roleName]);
        $role->addGroup($userGroup);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
    }

    public function removeRoleFromUserGroup(int $groupId, string $roleName): void
    {
        $userGroup = $this->findUserGroup($groupId);
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(["name" => $roleName]);
        $role->removeGroup($userGroup);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
    }
}