<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTime;

class InternshipCycleViewDTO
{
    public function __construct(
        public int $id,
        public DateTime $createdAt,
        public ?DateTime $endedAt,
        public ?DateTime $collectionStartDate,
        public ?DateTime $collectionEndDate,
        public ?DateTime $applicationStartDate,
        public ?DateTime $applicationEndDate,
        public string $partnerUserGroupName,
        public string $studentUserGroupName
    ) {
    }
}