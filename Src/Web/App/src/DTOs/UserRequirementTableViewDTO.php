<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTimeImmutable;

readonly class UserRequirementTableViewDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $requirementId,
        public string $requirementName,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public ?DateTimeImmutable $completedAt,
        public string $status,
    ) {
    }
}