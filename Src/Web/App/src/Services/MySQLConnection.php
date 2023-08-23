<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IDatabaseConnection;
use PDO;
use PDOException;

class MySQLConnection implements IDatabaseConnection
{
    private $connection;

    public function __construct(
        private string $host,
        private string $db,
        private string $user,
        private string $password
    ) {
        try {
            $this->connection = new PDO("mysql:host={$host};db={$db};charset=utf8", $user, $password);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}