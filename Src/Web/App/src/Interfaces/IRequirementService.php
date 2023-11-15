<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\RequirementDTO;
use App\Entities\Requirement;

interface IRequirementService
{
    public function getRequirements(): array;
    public function getRequirement(int $id): Requirement|null;
    public function createRequirement(RequirementDTO $requirementDTO): void;
    public function getUserRequirements(int $userId): array;
}