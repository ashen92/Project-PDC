<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;

class UserRequirementMapper implements IMapper
{
    #[\Override] public static function map(array $data): \App\Models\UserRequirement
    {
        return new \App\Models\UserRequirement(
            $data["id"],
            $data["user_id"],
            $data["requirement_id"],
            \App\Models\Requirement\FulFillMethod::fromString($data["fulfillMethod"]),
            new \DateTimeImmutable($data["startDate"]),
            new \DateTimeImmutable($data["endDate"]),
            $data["completedAt"] === null ? null : new \DateTimeImmutable($data["completedAt"]),
            $data["status"],
            $data["filePaths"] === null ? null : json_decode($data["filePaths"]),
            $data["textResponse"],
        );
    }
}