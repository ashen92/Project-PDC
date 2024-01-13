<?php
declare(strict_types=1);

namespace App\DTOs;

class CreateCycleDTO
{
    public function __construct(
        public string $collectionStartDate,
        public string $collectionEndDate,
        public string $applicationStartDate,
        public string $applicationEndDate,
        public int $partnerGroup,
        public int $studentGroup
    ) {
    }
}