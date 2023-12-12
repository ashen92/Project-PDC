<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTime;

class CreateRequirementDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public string $requirementType,
        public DateTime $startDate,
        public ?DateTime $endBeforeDate,
        public ?string $repeatInterval,
        public string $fulfillMethod,
        public ?array $allowedFileTypes,
        public ?int $maxFileSize,
        public ?int $maxFileCount,
    ) {

    }
}