<?php
declare(strict_types=1);

namespace DatabaseUpdateService;

use DateTimeImmutable;

class InternMapper
{
    public static function map(array $row): Intern
    {
        return new Intern(
            $row["id"],
            $row["student_id"],
            new DateTimeImmutable($row["createdAt"]),
            $row["internship_cycle_id"]
        );
    }
}