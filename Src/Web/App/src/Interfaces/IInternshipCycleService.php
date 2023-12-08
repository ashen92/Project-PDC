<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateInternshipCycleDTO;
use App\Entities\InternshipCycle;

interface IInternshipCycleService
{
    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle;
    public function getStudentUsers(int $internshipCycleId): array;
}