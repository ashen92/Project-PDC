<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTime;

class UserRequirementViewDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public DateTime $startDate,
        public DateTime $endDate,
        public ?DateTime $completedAt,
        public string $status,
        public string $fulfillMethod,
        public ?array $allowedFileTypes,
        public ?int $maxFileSize,
        public ?int $maxFileCount,
    ) {

    }
}