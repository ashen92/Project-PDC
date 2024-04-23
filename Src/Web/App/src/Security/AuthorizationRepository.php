<?php
declare(strict_types=1);

namespace App\Security;

use App\Interfaces\IRepository;
use PDO;

readonly class AuthorizationRepository implements IRepository
{
    public function __construct(
        private PDO $pdo,
    ) {

    }

    function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    function commit(): void
    {
        $this->pdo->commit();
    }

    function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function hasRole(int $userId, string $role): bool
    {
        $sql = "SELECT COUNT(*) FROM users u
                INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
                INNER JOIN user_group_roles ugr ON ugm.usergroup_id = ugr.usergroup_id
                INNER JOIN roles r ON ugr.role_id = r.id
                WHERE u.id = :userId AND r.name = :roleName";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':roleName', $role);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    /**
     * @return array<string>
     */
    public function findUserRoles(int $userId): array
    {
        $sql = "SELECT r.* FROM users u
                INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
                INNER JOIN user_group_roles ugr ON ugm.usergroup_id = ugr.usergroup_id
                INNER JOIN roles r ON ugr.role_id = r.id
                WHERE u.id = :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasPermission(int $userId, string $name): bool
    {
        $sql = 'SELECT COUNT(*) FROM permissions p
        LEFT JOIN role_permissions rp ON p.id = rp.permission_id
        LEFT JOIN user_permissions up ON p.id = up.permission_id
        LEFT JOIN user_group_roles ugr ON rp.role_id = ugr.role_id
        LEFT JOIN user_group_membership ugm ON ugr.usergroup_id = ugm.usergroup_id
        LEFT JOIN users u ON ugm.user_id = u.id
        WHERE u.id = :userId AND p.name = :permissionName';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':permissionName', $name);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}