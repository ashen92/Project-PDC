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

    public function hasRole(int $userId, Role $role): bool
    {
        $sql = "SELECT COUNT(*) FROM users u
                INNER JOIN user_group_membership ugm ON u.id = ugm.user_id
                INNER JOIN user_groups ug ON ugm.usergroup_id = ug.id
                INNER JOIN user_group_roles ugr ON ug.id = ugr.usergroup_id
                INNER JOIN roles r ON ugr.role_id = r.id
                WHERE u.id = :userId AND r.name = :roleName";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':roleName', $role->value);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    /**
     * @return array<Role>
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

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => Role::tryFrom($row['name']), $data);
    }
}