<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateInternshipCycleDTO;
use App\DTOs\InternshipCycleViewDTO;
use App\Entities\InternshipCycle;

interface IInternshipCycleService
{
    public function getLatestInternshipCycleId(): ?int;
    public function getLatestInternshipCycle(): ?InternshipCycleViewDTO;
    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle;
    public function getStudentUsers(?int $internshipCycleId = null): array;
}