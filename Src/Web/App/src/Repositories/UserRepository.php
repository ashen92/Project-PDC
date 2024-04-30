<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateUserDTO;
use App\Interfaces\IRepository;
use App\Mappers\PartnerMapper;
use App\Mappers\StudentMapper;
use App\Mappers\UserGroupMapper;
use App\Mappers\UserMapper;
use App\Mappers\UserStudentPartnerMapper;
use App\Models\Student;
use App\Models\User;
use App\Models\UserGroup;
use PDO;

class UserRepository implements IRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function findUser(int $userId): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userId" => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return UserMapper::map($data);
    }

    public function findStudent(int $userId): ?Student
    {
        $sql = "SELECT u.*, s.* FROM students s INNER JOIN users u on s.id = u.id WHERE u.id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userId" => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return StudentMapper::map($data);
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
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return UserMapper::map($data);
    }

    public function findByActivationToken(string $token): ?User
    {
        $sql = "SELECT * FROM users WHERE activationToken = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["token" => $token]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return UserMapper::map($data);
    }

    public function findStudentByStudentEmail(string $email): ?Student
    {
        $sql = "SELECT u.*, s.* FROM students s INNER JOIN users u on s.id = u.id WHERE studentEmail = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return StudentMapper::map($data);
    }

    public function findManagedUsers(int $userId): array
    {
        $sql = "SELECT u.*, p.* FROM users u
                JOIN partners p ON u.id = p.id
                WHERE p.managedBy_id = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userId" => $userId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($user) => PartnerMapper::map($user), $data);
    }

    public function doesUserExist(string $email, bool $isStudentEmail = false): bool
    {
        if ($isStudentEmail) {
            $sql = "SELECT COUNT(*) FROM students WHERE studentEmail = :email";
        } else {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $data = $stmt->fetch(PDO::FETCH_COLUMN);
        return $data > 0;
    }

    /**
     * @return int The ID of the created user
     */
    public function createUser(CreateUserDTO $dto): int
    {
        // TODO: Implement throwing exceptions when user creation fails

        if ($dto->userType === User\Type::STUDENT->value) {

            $sql = "INSERT INTO users (isActive, type)
                    VALUES (:isActive, :type)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue("isActive", 0, PDO::PARAM_INT);
            $stmt->bindValue("type", User\Type::STUDENT->value);
            $stmt->execute();

            $userId = (int) $this->pdo->lastInsertId();

            $sql = "INSERT INTO students (id, studentEmail, fullName, registrationNumber, indexNumber)
                    VALUES (:id, :studentEmail, :fullName, :registrationNumber, :indexNumber)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue("id", $userId, PDO::PARAM_INT);
            $stmt->bindValue("studentEmail", $dto->studentEmail);
            $stmt->bindValue("fullName", $dto->fullName);
            $stmt->bindValue("registrationNumber", $dto->registrationNumber);
            $stmt->bindValue("indexNumber", $dto->indexNumber);
            $stmt->execute();

            return $userId;
        }

        if ($dto->userType === User\Type::PARTNER->value) {

            $sql = "INSERT INTO users (email, firstName,  isActive, type)
                    VALUES (:email, :firstName, :isActive, :type)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue("email", $dto->email);
            $stmt->bindValue("firstName", $dto->firstName);
            $stmt->bindValue("isActive", 0, PDO::PARAM_INT);
            $stmt->bindValue("type", User\Type::PARTNER->value);
            $stmt->execute();

            $userId = (int) $this->pdo->lastInsertId();

            $sql = "INSERT INTO partners (id, organization_id)
                    VALUES (:id, :organizationId)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue("id", $userId, PDO::PARAM_INT);
            $stmt->bindValue("organizationId", $dto->organizationId, PDO::PARAM_INT);
            $stmt->execute();

            return $userId;
        }

        $sql = "INSERT INTO users (email, firstName, isActive, type)
                VALUES (:email, :firstName, :isActive, :type)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("email", $dto->email);
        $stmt->bindValue("firstName", $dto->firstName);
        $stmt->bindValue("isActive", 0, PDO::PARAM_INT);
        $stmt->bindValue("type", User\Type::USER->value);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function updateUser(User $user): void
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
                    ?->format($this::DATE_TIME_FORMAT),
        ]);
    }

    public function findUserGroupByName(string $name): ?UserGroup
    {
        $sql = "SELECT * FROM user_groups WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["name" => $name]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return null;
        }
        return UserGroupMapper::map($data);
    }

    /**
     * @return array<UserGroup>
     */
    public function findAllUserGroups(): array
    {
        $sql = "SELECT * FROM user_groups";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($group) => UserGroupMapper::map($group), $data);
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

    public function createUserGroup(string $groupName): UserGroup
    {
        $sql = "INSERT INTO user_groups (name) VALUES (:name)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(["name" => $groupName]);
        return new UserGroup(
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

    public function checkUserGroupMember(int $userid, int $groupid): bool
    {
        $sql = "SELECT * FROM user_group_membership WHERE user_id = :userid AND usergroup_id = :groupid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["userid" => $userid, "groupid" => $groupid]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return true;
        } else {
            return false;
        }
    }

    public function addRoleToUserGroup(int $groupId, string $role): bool
    {
        // TODO: Move this to authorization service. This is not a user repository concern
        // TODO: Check if the role exists
        $sql = "INSERT INTO user_group_roles (usergroup_id, role_id)
                SELECT :groupId, id FROM roles WHERE name = :name";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([
            "groupId" => $groupId,
            "name" => $role,
        ]);
    }

    public function removeRoleFromUserGroup(int $groupId, string $role): bool
    {
        // TODO: Move this to authorization service. This is not a user repository concern
        // TODO: Check if the role exists
        $sql = "DELETE FROM user_group_roles
                WHERE usergroup_id = :groupId
                AND role_id = (SELECT id FROM roles WHERE name = :roleName)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("groupId", $groupId, PDO::PARAM_INT);
        $stmt->bindValue("roleName", $role);
        return $stmt->execute();
    }

    public function searchUsers(int $limit, int $offsetBy): array
    {
        $sql = "SELECT u.id AS user_id, u.*, s.*, p.* FROM users u
            LEFT JOIN students s ON u.id = s.id 
            LEFT JOIN partners p ON u.id = p.id
            GROUP BY u.id";
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        if ($offsetBy !== 0) {
            $sql .= " OFFSET :offsetBy";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue("limit", $limit, PDO::PARAM_INT);
        }
        if ($offsetBy !== 0) {
            $stmt->bindValue("offsetBy", $offsetBy, PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($user) => UserStudentPartnerMapper::map($user), $data);
    }


    public function searchGroups(?int $numberOfResults, ?int $offsetBy): array
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
            $stmt->bindValue("numberOfResults", $numberOfResults, PDO::PARAM_INT);
        }
        if ($offsetBy !== null) {
            $stmt->bindValue("offsetBy", $offsetBy, PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return array_map(fn($group) => UserGroupMapper::map($group), $data);
    }

    public function managePartner(int $managedBy, int $partnerId): bool
    {
        $sql = "UPDATE partners SET managedBy_id = :managedBy WHERE id = :partnerId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "managedBy" => $managedBy,
            "partnerId" => $partnerId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function findActiveUsers(): array
    {
        $sql = "SELECT * FROM users WHERE isActive = 1";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($data as $userData) {
            $users[] = UserMapper::map($userData);
        }

        return $users;
    }

    public function findStudentUsers(): array
    {
        $sql = "SELECT u.*, s.*
                FROM users u 
                JOIN students s ON u.id = s.id 
                WHERE u.isActive = 1 AND u.type = 'student'";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        if ($data === false) {
            return [];
        }
        return array_map(fn($user) => StudentMapper::map($user), $data);
    }

    public function findCoordinators(): array
    {
        $sql = "SELECT * FROM users WHERE isActive = 1 AND type ='user' AND firstName LIKE 'C%'";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($data as $userData) {
            $users[] = UserMapper::map($userData);
        }

        return $users;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function activate(int $id): bool
    {
        $sql = 'UPDATE users SET isActive = 1 WHERE id = :id AND isActive = 0';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): bool
    {
        $sql = 'UPDATE users SET isActive = 0 WHERE id = :id AND isActive = 1';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findAllPartners(): array
    {
        $sql = "SELECT o.*, p.* FROM organizations o
                JOIN partners p ON o.id = p.organization_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return $data;
    }

    public function getGroupName($groupid)
    {
        $sql = "SELECT * FROM user_groups 
                WHERE id = :groupid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['groupid' => $groupid]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return $data;
    }

    public function getGroupUsers($groupid)
    {
        $sql = "SELECT m.*, u.* FROM user_group_membership m
                JOIN users u ON m.user_id = u.id
                WHERE m.usergroup_id = :groupid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['groupid' => $groupid]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($data === false) {
            return [];
        }
        return $data;
    }
}