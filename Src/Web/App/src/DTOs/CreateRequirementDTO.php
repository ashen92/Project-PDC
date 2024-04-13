<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;
use DateTimeImmutable;

class CreateRequirementDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public Type $requirementType,
        public DateTimeImmutable $startDate,
        public ?DateTimeImmutable $endBeforeDate,
        public ?RepeatInterval $repeatInterval,
        public FulFillMethod $fulfillMethod,
        public ?string $allowedFileTypes,
        public ?int $maxFileSize,
        public ?int $maxFileCount,
    ) {

    }
}