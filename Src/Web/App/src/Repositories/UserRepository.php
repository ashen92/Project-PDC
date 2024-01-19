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

    public function findUser(int $userId): ?\App\Models\User
    {
        $sql = "SELECT * FROM users WHERE id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userId" => $userId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return \App\Mappers\UserMapper::map($data);
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

    public function findStudentByStudentEmail(string $email): ?\App\Models\Student
    {
        $sql = "SELECT u.*, s.* FROM students s INNER JOIN users u on s.id = u.id WHERE studentEmail = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return \App\Mappers\StudentMapper::map($data);
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

    /**
     * @return array<\App\Models\UserGroup>
     */
    public function findAllUserGroups(): array
    {
        $sql = "SELECT * FROM user_groups";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($group) => \App\Mappers\UserGroupMapper::map($group), $data);
    }

    public function findRoleByName(string $roleName): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy(["name" => $roleName]);
    }

    public function addToUserGroup(int $userId, int $groupId): void
    {
        $sql = "INSERT INTO user_group_membership (user_id, usergroup_id) VALUES (:userId, :groupId)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "userId" => $userId,
            "groupId" => $groupId,
        ]);
    }

    public function createUserGroup(string $groupName): \App\Models\UserGroup
    {
        $sql = "INSERT INTO user_groups (name) VALUES (:name)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(["name" => $groupName]);
        return new \App\Models\UserGroup(
            (int) $this->pdo->lastInsertId(),
            $groupName,
        );
    }

    public function addUsersToUserGroup(int $groupId, int $fromUserGroupId): bool
    {
        $sql = "INSERT INTO user_group_membership (user_id, usergroup_id)
                SELECT user_id, :groupId FROM user_group_membership WHERE usergroup_id = :fromUserGroupId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            "groupId" => $groupId,
            "fromUserGroupId" => $fromUserGroupId,
        ]);
    }

    public function addRoleToUserGroup(int $groupId, \App\Security\Role $role): bool
    {
        $sql = "INSERT INTO user_group_roles (usergroup_id, role_id)
                SELECT :groupId, id FROM roles WHERE name = :name";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([
            "groupId" => $groupId,
            "name" => $role->value,
        ]);
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

    public function searchUsers(?int $numberOfResults, ?int $offsetBy): array
    {
        $sql = "SELECT u.id AS user_id, u.*, s.*, p.* FROM users u
            LEFT JOIN students s ON u.id = s.id 
            LEFT JOIN partners p ON u.id = p.id";
        if ($numberOfResults !== null) {
            $sql .= " LIMIT :numberOfResults";
        }
        if ($offsetBy !== null) {
            $sql .= " OFFSET :offsetBy";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($numberOfResults !== null) {
            $stmt->bindValue("numberOfResults", $numberOfResults, \PDO::PARAM_INT);
        }
        if ($offsetBy !== null) {
            $stmt->bindValue("offsetBy", $offsetBy, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($user) => \App\Mappers\UserStudentPartnerMapper::map($user), $data);
    }

    public function searchGroups(?int $numberOfResults, ?int $offsetBy)
    {
        $sql = "SELECT * FROM user_groups";
        if ($numberOfResults !== null) {
            $sql .= " LIMIT :numberOfResults";
        }
        if ($offsetBy !== null) {
            $sql .= " OFFSET :offsetBy";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($numberOfResults !== null) {
            $stmt->bindValue("numberOfResults", $numberOfResults, \PDO::PARAM_INT);
        }
        if ($offsetBy !== null) {
            $stmt->bindValue("offsetBy", $offsetBy, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($group) => \App\Mappers\UserGroupMapper::map($group), $data);
    }
}