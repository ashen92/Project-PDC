<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateUserDTO;
use App\Entities\Partner;
use App\Entities\Permission;
use App\Entities\Role;
use App\Entities\Student;
use App\Entities\User;
use App\Entities\UserGroup;
use App\Interfaces\Repository\IRepository;
use App\Security\Permission\Action;
use App\Security\Permission\Resource;

class UserRepository extends Repository implements IRepository
{
    public function __construct(
        private readonly \PDO $pdo,
        \Doctrine\ORM\EntityManager $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function find(int $userId): null|User|Student|Partner
    {
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

    /**
     * @return array<string>
     */
    public function findUserRoles(int $userId): array
    {
        $sql = "SELECT r.name FROM users u
                JOIN user_group_membership ugm ON u.id = ugm.user_id
                JOIN user_group_roles ugr ON ugm.usergroup_id = ugr.usergroup_id
                JOIN roles r ON ugr.role_id = r.id
                WHERE u.id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userId" => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function findByEmail(string $email): ?\App\Models\User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return \App\Mappers\UserMapper::map($data);
    }

    public function findByStudentEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(Student::class)->findOneBy(["studentEmail" => $email]);
    }

    public function findByActivationToken(string $token): ?\App\Models\User
    {
        $sql = "SELECT * FROM users WHERE activationToken = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["token" => $token]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return \App\Mappers\UserMapper::map($data);
    }

    public function findManagedUsers(int $userId): array
    {
        return $this->entityManager->getRepository(Partner::class)->findBy(["managedBy" => $userId]);
    }

    public function createUser(CreateUserDTO $dto): null|User|Student|Partner
    {
        if ($dto->userType == "student") {
            $user = new Student(
                $dto->studentEmail,
                $dto->fullName,
                $dto->registrationNumber,
                $dto->indexNumber,
            );
        } else if ($dto->userType == "partner") {
            $user = new Partner(
                $dto->email,
                $dto->firstName,
            );
            // TODO: add partner specific fields
        } else {
            $user = new User(
                $dto->email,
                $dto->firstName,
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateUser(\App\Models\User $user): void
    {
        $sql = "UPDATE users SET
                email = :email,
                firstName = :firstName,
                lastName = :lastName,
                passwordHash = :passwordHash,
                isActive = :isActive,
                activationToken = :activationToken,
                activationTokenExpiresAt = :activationTokenExpiresAt
                WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "firstName" => $user->getFirstName(),
            "lastName" => $user->getLastName(),
            "passwordHash" => $user->getPasswordHash(),
            "isActive" => $user->isActive() ? 1 : 0,
            "activationToken" => $user->getActivationToken(),
            "activationTokenExpiresAt" => $user
                ->getActivationTokenExpiresAt()
                    ?->format(self::DATE_TIME_FORMAT),
        ]);
    }

    public function findUserGroup(int $groupId): ?UserGroup
    {
        return $this->entityManager->getRepository(UserGroup::class)->find($groupId);
    }

    public function findUserGroupByName(string $groupName): ?UserGroup
    {
        return $this->entityManager->getRepository(UserGroup::class)->findOneBy(["name" => $groupName]);
    }

    public function findAllUserGroups(): array
    {
        return $this->entityManager->getRepository(UserGroup::class)->findAll();
    }

    public function findRoleByName(string $roleName): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy(["name" => $roleName]);
    }

    public function addToUserGroup(int $userId, int $groupId): void
    {
        $user = $this->find($userId);
        $userGroup = $this->findUserGroup($groupId);
        $userGroup->addUser($user);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();
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

    public function addPermissionToRole(string $role, Resource $resource, Action $action): void
    {
        $role = $this->findRoleByName($role);
        $permission = new Permission($resource, $action);
        $role->addPermission($permission);
        $this->entityManager->persist($permission);
        $this->entityManager->persist($role);
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