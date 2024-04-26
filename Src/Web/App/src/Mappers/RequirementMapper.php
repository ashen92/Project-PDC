<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\Requirement;
use DateInterval;

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
            $row["requirementType"],
            DateInterval::createFromDateString($row["startWeek"]),
            DateInterval::createFromDateString($row["durationWeeks"]),
            $row["repeatInterval"] ? $row["repeatInterval"] : null,
            $row["fulfillMethod"],
            $row['allowedFileTypes'] ? json_decode($row['allowedFileTypes'], false) : null,
            $row["maxFileSize"],
            $row["maxFileCount"],
            $row["internship_cycle_id"]
        );
    }
}