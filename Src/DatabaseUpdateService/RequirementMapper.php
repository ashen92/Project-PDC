<?php
declare(strict_types=1);

namespace DatabaseUpdateService;

use DateInterval;

class RequirementMapper
{
    public static function map(array $row): Requirement
    {
        return new Requirement(
            $row["id"],
            DateInterval::createFromDateString($row["startWeek"]),
            DateInterval::createFromDateString($row["durationWeeks"]),
            $row["fulfillMethod"],
            $row["internship_cycle_id"]
        );
    }
}