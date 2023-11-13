<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTime;

class RequirementDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public string $type,
        public DateTime $startDate,
        public DateTime|null $endBeforeDate,
        public string|null $repeatInterval,
    ) {

    }
}