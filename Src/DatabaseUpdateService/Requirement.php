<?php
declare(strict_types=1);

namespace DatabaseUpdateService;

use DateInterval;

readonly class Requirement
{
    public function __construct(
        public int $id,
        public DateInterval $startWeek,
        public DateInterval $durationWeeks,
        public string $fulfillMethod,
        public int $internshipCycleId
    ) {
    }
}