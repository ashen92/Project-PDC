<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementCompletionDTO;
use App\DTOs\UserRequirementViewDTO;
use App\Entities\Requirement;

interface IRequirementService
{
    public function getRequirements(): array;
    public function getRequirement(int $id): ?Requirement;
    public function createRequirement(CreateRequirementDTO $requirementDTO): void;
    public function getUserRequirements(int $userId): array;
    public function getUserRequirement(int $id): UserRequirementViewDTO|null;
    public function completeUserRequirement(UserRequirementCompletionDTO $completeUserRequirementDTO): void;
}