<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IRepository
{
    public const DATE_TIME_FORMAT = "Y-m-d H:i:s";

    public function beginTransaction(): void;
    public function commit(): bool;
    public function rollBack(): void;
}