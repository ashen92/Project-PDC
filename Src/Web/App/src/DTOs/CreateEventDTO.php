<?php
declare(strict_types=1);

namespace App\DTOs;
use DateTimeImmutable;

readonly class CreateEventDTO
{
    public function __construct(
        public string $title,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public string $eventLocation,
        public string $description,
        public array $participants
    ) {
    }
}