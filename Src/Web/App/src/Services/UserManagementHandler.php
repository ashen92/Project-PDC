<?php
declare(strict_types=1);

namespace App\Services;

use PDO;
use stdClass;

class UserManagementHandler
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * @param array<stdClass> $data
     * @return bool
     */
    public function updateUsers(array $data): bool
    {
        $this->pdo->beginTransaction();
        try {
            foreach ($data as $i) {
                $sql = "UPDATE users SET ";
                if (property_exists($i, 'isActive')) {
                    $sql .= "isActive = :isActive";
                }
                if (property_exists($i, 'isDisabled')) {
                    $sql .= ", isDisabled = :isDisabled";
                }
                $sql .= " WHERE id = :id";

                $stmt = $this->pdo->prepare($sql);
                if (property_exists($i, 'isActive')) {
                    $stmt->bindValue(':isActive', $i->isActive, PDO::PARAM_BOOL);
                }
                if (property_exists($i, 'isDisabled')) {
                    $stmt->bindValue(':isDisabled', $i->isDisabled, PDO::PARAM_BOOL);
                }
                $stmt->bindValue(':id', $i->id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    $this->pdo->rollBack();
                    return false;
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return false;
        }
    }
}