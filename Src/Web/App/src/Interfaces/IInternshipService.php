<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IInternshipService
{
    public function getInternships(): array;
}