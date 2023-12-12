<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTime;

class CreateUserRequirementDTO
{
    public function __construct(
        public DateTime $startDate,
        public DateTime $endDate,
        public ?string $status = "pending",
    ) {
    }
}