<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\Internship;

interface IInternshipService
{
    public function getInternships(): array;
    public function getInternshipById(int $id): Internship|null;
}