<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTimeImmutable;

readonly class CreateSessionTitleDTO
{
    public function __construct(
        public string $companyname,
        public string $title,
        //public DateTimeImmutable $startTime,
        //public DateTimeImmutable $endTime,
        //public string $sessionLocation,
        public string $description,
        //public array $participants
    ) {
    }
}