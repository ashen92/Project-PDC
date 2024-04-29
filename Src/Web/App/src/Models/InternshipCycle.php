<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class InternshipCycle
{
    public function __construct(
        private int $id,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $endedAt,
        private ?DateTimeImmutable $jobCollectionStart,
        private ?DateTimeImmutable $jobCollectionEnd,
        private ?DateTimeImmutable $jobHuntRound1Start,
        private ?DateTimeImmutable $jobHuntRound1End,
        private ?DateTimeImmutable $jobHuntRound2Start,
        private ?DateTimeImmutable $jobHuntRound2End,
        private array $partnerGroupIds,
        private ?int $studentGroupId,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getJobCollectionStart(): ?DateTimeImmutable
    {
        return $this->jobCollectionStart;
    }

    public function getJobCollectionEnd(): ?DateTimeImmutable
    {
        return $this->jobCollectionEnd;
    }

    public function getJobHuntRound1Start(): ?DateTimeImmutable
    {
        return $this->jobHuntRound1Start;
    }

    public function getJobHuntRound1End(): ?DateTimeImmutable
    {
        return $this->jobHuntRound1End;
    }

    public function getJobHuntRound2Start(): ?DateTimeImmutable
    {
        return $this->jobHuntRound2Start;
    }

    public function getJobHuntRound2End(): ?DateTimeImmutable
    {
        return $this->jobHuntRound2End;
    }

    public function getPartnerGroupIds(): array
    {
        return $this->partnerGroupIds;
    }

    public function getStudentGroupId(): ?int
    {
        return $this->studentGroupId;
    }

    public function isFirstRound(): bool
    {
        return $this->jobHuntRound1Start !== null && $this->jobHuntRound1End === null;
    }

    public function isSecondRound(): bool
    {
        return $this->jobHuntRound2Start !== null && $this->jobHuntRound2End === null;
    }

    public function isJobCollectionPhase(): bool
    {
        return $this->jobCollectionStart !== null && $this->jobCollectionEnd === null;
    }

    public function isFirstRoundPhase(): bool
    {
        return $this->jobHuntRound1Start !== null && $this->jobHuntRound1End === null;
    }

    public function isSecondRoundPhase(): bool
    {
        return $this->jobHuntRound2Start !== null && $this->jobHuntRound2End === null;
    }
}