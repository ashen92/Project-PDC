<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Requirement\FulFillMethod;
use DateInterval;

class CreateRequirementDTO
{
    public DateInterval $startWeek;
    public DateInterval $durationWeeks;
    public function __construct(
        public string $name,
        public string $description,
        private int $startWeekAsInt,
        private int $durationWeeksAsInt,
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