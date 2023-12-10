<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateInternshipCycleDTO;
use App\Entities\InternshipCycle;

interface IInternshipCycleService
{
    public function getLatestInternshipCycleId(): ?int;
    public function getLatestInternshipCycle(): ?InternshipCycle;
    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle;
    public function endInternshipCycle(?int $id = null): bool;
    public function getStudentUsers(?int $internshipCycleId = null): array;
}