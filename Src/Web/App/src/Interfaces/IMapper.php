<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IMapper
{
    /**
     * @param array<string, mixed> $row
     */
    public static function map(array $row): mixed;
}