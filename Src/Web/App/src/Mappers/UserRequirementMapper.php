<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;

class UserRequirementMapper implements IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): \App\Models\UserRequirement
    {
        return new \App\Models\UserRequirement(
            $row["id"],
            $row["user_id"],
            $row["requirement_id"],
            \App\Models\Requirement\FulFillMethod::tryFrom($row["fulfillMethod"]),
            new \DateTimeImmutable($row["startDate"]),
            new \DateTimeImmutable($row["endDate"]),
            $row["completedAt"] === null ? null : new \DateTimeImmutable($row["completedAt"]),
            $row["status"],
            $row["textResponse"],
        );
    }
}