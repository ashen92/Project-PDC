<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementCompletionDTO;
use App\Entities\Requirement;
use App\Entities\UserRequirement;

interface IRequirementService
{
    /**
     * @return array<string,string>
     */
    public function getRequirements(): array;

    public function getRequirement(int $id): ?Requirement;
    public function createRequirement(CreateRequirementDTO $requirementDTO): void;

    /**
     * @return array<\App\Entities\UserRequirement>
     */
    public function getUserRequirements(
        ?int $internshipCycleId = null,
        ?int $requirementId = null,
        ?int $userId = null,
        ?string $status = null
    ): array;

    public function getUserRequirement(int $id): ?UserRequirement;
    public function completeUserRequirement(UserRequirementCompletionDTO $completeUserRequirementDTO): bool;
}