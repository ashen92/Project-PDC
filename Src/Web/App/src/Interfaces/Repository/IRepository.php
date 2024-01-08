<?php
declare(strict_types=1);

namespace App\Interfaces\Repository;

interface IRepository
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}