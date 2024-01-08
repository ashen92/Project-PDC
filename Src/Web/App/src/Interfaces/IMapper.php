<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public static function map(array $data): mixed;
}