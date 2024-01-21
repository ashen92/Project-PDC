<?php
declare(strict_types=1);

namespace App\Security;

use PDO;

readonly class IdentityProvider
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function getIdentity(int $userId): Identity
    {
        $sql = "SELECT r.name FROM users u
                JOIN user_group_membership ugm ON u.id = ugm.user_id
                JOIN user_group_roles ugr ON ugm.usergroup_id = ugr.usergroup_id
                JOIN roles r ON ugr.role_id = r.id
                WHERE u.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id" => $userId,
        ]);
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return new Identity(array_map(fn($role) => Role::tryFrom($role), $roles));
    }
}