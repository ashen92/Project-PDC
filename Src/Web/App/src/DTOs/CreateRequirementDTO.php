<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;
use DateInterval;

class CreateRequirementDTO
{
    public DateInterval $startWeek;
    public DateInterval $durationWeeks;
    public function __construct(
        public string $name,
        public string $description,
        public Type $requirementType,
        private int $startWeekAsInt,
        private int $durationWeeksAsInt,
        public ?RepeatInterval $repeatInterval,
        public FulFillMethod $fulfillMethod,
        public ?array $allowedFileTypes,
        public ?int $maxFileSize,
        public ?int $maxFileCount,
    ) {
        $week = $this->startWeekAsInt - 1;
        $this->startWeek = new DateInterval("P{$week}W");
        $this->durationWeeks = new DateInterval("P{$this->durationWeeksAsInt}W");
    }
}