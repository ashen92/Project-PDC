<?php
declare(strict_types=1);

namespace App\Interfaces\Repository;

interface IRepository
{
    public const DATE_TIME_FORMAT = "Y-m-d H:i:s";

    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}