<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Requirement\FulFillMethod;
use DateTimeImmutable;

class UserRequirement
{
    public function __construct(
        private int $id,
        private int $userId,
        private int $requirementId,
        private FulFillMethod $fulfillMethod,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
        private ?DateTimeImmutable $completedAt,
        private string $status,
        private ?string $textResponse,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRequirement(): int
    {
        return $this->requirementId;
    }

    public function getFulfillMethod(): FulFillMethod
    {
        return $this->fulfillMethod;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTextResponse(): ?string
    {
        return $this->textResponse;
    }
}