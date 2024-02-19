<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;

class Requirement
{
    // A requirement can be repeated up to 6 months after the start date.
    public const MAXIMUM_REPEAT_DURATION = "P6M";

    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private Type $requirementType,
        private \DateTimeImmutable $startDate,
        private ?\DateTimeImmutable $endBeforeDate,
        private ?RepeatInterval $repeatInterval,
        private FulFillMethod $fulfillMethod,
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

    public function getRequirementType(): Type
    {
        return $this->requirementType;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndBeforeDate(): ?\DateTimeImmutable
    {
        return $this->endBeforeDate;
    }

    public function getRepeatInterval(): ?RepeatInterval
    {
        return $this->repeatInterval;
    }

    public function getFulfillMethod(): FulFillMethod
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