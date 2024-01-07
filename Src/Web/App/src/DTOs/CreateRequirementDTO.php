<?php
declare(strict_types=1);

namespace App\DTOs;

use DateTimeImmutable;

class CreateRequirementDTO
{
    /**
     * @param array<string> $allowedFileTypes
     */
    public function __construct(
        public string $name,
        public string $description,
        public string $requirementType,
        public DateTimeImmutable $startDate,
        public ?DateTimeImmutable $endBeforeDate,
        public ?string $repeatInterval,
        public string $fulfillMethod,
        public ?array $allowedFileTypes,
        public ?int $maxFileSize,
        public ?int $maxFileCount,
    ) {

    }
}