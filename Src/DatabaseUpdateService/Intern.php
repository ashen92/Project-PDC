<?php
declare(strict_types=1);

namespace DatabaseUpdateService;

use DateTimeImmutable;

readonly class Intern
{
    public function __construct(
        public int $id,
        public int $studentId,
        public DateTimeImmutable $createdAt,
        public int $cycleId
    ) {
    }
}