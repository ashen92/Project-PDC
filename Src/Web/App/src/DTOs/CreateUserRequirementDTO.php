<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Requirement\RepeatInterval;
use DateInterval;
use DateTimeImmutable;

class CreateUserRequirementDTO
{
    public readonly DateTimeImmutable $endDate;
    public function __construct(
        public readonly DateTimeImmutable $startDate,
        public readonly RepeatInterval $repeatInterval,
        public readonly ?string $status = "pending",
    ) {
        $this->endDate = $startDate->add(new DateInterval($repeatInterval->toDuration()));
    }
}