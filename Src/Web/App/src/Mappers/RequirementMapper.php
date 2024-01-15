<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\Requirement;

class RequirementMapper implements IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): Requirement
    {
        return new Requirement(
            $row["id"],
            $row["name"],
            $row["description"],
            Requirement\Type::fromString($row["requirementType"]),
            new \DateTimeImmutable($row["startDate"]),
            $row["endBeforeDate"] === null ? null : new \DateTimeImmutable($row["endBeforeDate"]),
            $row["repeatInterval"] === null ? null : Requirement\RepeatInterval::fromString($row["repeatInterval"]),
            Requirement\FulFillMethod::fromString($row["fulfillMethod"]),
            $row["allowedFileTypes"] === null ? null : explode(",", $row["allowedFileTypes"]),
            $row["maxFileSize"],
            $row["maxFileCount"],
            $row["internship_cycle_id"]
        );
    }
}