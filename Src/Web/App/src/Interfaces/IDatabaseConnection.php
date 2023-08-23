<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IDatabaseConnection
{
    public function getConnection();
}