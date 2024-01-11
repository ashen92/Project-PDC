<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\Requirement;

class RequirementMapper implements IMapper
{
    public static function map(array $data): Requirement
    {
        return new Requirement(
            $data["id"],
            $data["name"],
            $data["description"],
            Requirement\Type::fromString($data["requirementType"]),
            new \DateTimeImmutable($data["startDate"]),
            $data["endBeforeDate"] === null ? null : new \DateTimeImmutable($data["endBeforeDate"]),
            $data["repeatInterval"] === null ? null : Requirement\RepeatInterval::fromString($data["repeatInterval"]),
            Requirement\FulFillMethod::fromString($data["fulfillMethod"]),
            $data["allowedFileTypes"] === null ? null : explode(",", $data["allowedFileTypes"]),
            $data["maxFileSize"],
            $data["maxFileCount"],
            $data["internship_cycle_id"]
        );
    }
}