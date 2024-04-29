<?php
declare(strict_types=1);

namespace App\Models;

use DateInterval;

class Requirement
{
    // A requirement can be repeated up to 6 months after the start date.
    public const MAXIMUM_REPEAT_DURATION = 'P6M';
    private const MAXIMUM_WEEKS = 24;

    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private DateInterval $startWeek,
        private DateInterval $durationWeeks,
        private string $fulfillMethod,
        private ?array $allowedFileTypes,
        private ?int $maxFileSize,
        private ?int $maxFileCount,
        private int $internshipCycleId
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartWeek(): DateInterval
    {
        return $this->startWeek;
    }

    public function getDurationWeeks(): DateInterval
    {
        return $this->durationWeeks;
    }

    public function getStartWeekAsMonthWeek(): string
    {
        $days = $this->startWeek->d;
        $weeks = $days / 7;
        $month = ceil($weeks / 4);
        if ($weeks % 4 === 0) {
            $month++;
        }
        $week = $weeks % 4 + 1;
        return "Month $month, Week $week";
    }

    public function getDurationWeeksAsWeeks(): string
    {
        $days = $this->durationWeeks->d;
        $weeks = $days / 7;
        return "$weeks weeks";
    }

    public function getFulfillMethod(): string
    {
        return $this->fulfillMethod;
    }

    public function getAllowedFileTypes(): ?array
    {
        return $this->allowedFileTypes;
    }

    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }

    public function getMaxFileCount(): ?int
    {
        return $this->maxFileCount;
    }

    public function getInternshipCycleId(): int
    {
        return $this->internshipCycleId;
    }
}