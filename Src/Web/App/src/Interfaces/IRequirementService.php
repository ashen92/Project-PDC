<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;

interface IRequirementService
{
    /**
     * @return array<\App\Models\Requirement>
     */
    public function getRequirements(): array;

    public function getRequirement(int $id): ?\App\Models\Requirement;
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

    public function getUserRequirement(int $id): ?\App\Models\UserRequirement;
    public function completeUserRequirement(UserRequirementFulfillmentDTO $dto): bool;
}