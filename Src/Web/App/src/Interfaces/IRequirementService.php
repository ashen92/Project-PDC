<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\RequirementDTO;

interface IRequirementService
{
    public function getRequirements(): array;
    public function createRequirement(RequirementDTO $requirementDTO): void;
}