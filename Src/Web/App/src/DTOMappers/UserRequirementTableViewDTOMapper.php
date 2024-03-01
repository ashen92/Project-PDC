<?php
declare(strict_types=1);

namespace App\DTOMappers;

use App\DTOs\UserRequirementTableViewDTO;
use App\Interfaces\IMapper;
use DateTimeImmutable;

class UserRequirementTableViewDTOMapper implements IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): UserRequirementTableViewDTO
    {
        return new UserRequirementTableViewDTO(
            $row["id"],
            $row["user_id"],
            $row["requirement_id"],
            $row["requirementName"],
            new DateTimeImmutable($row["startDate"]),
            new DateTimeImmutable($row["endDate"]),
            $row["completedAt"] === null ? null : new DateTimeImmutable($row["completedAt"]),
            $row["status"],
        );
    }
}