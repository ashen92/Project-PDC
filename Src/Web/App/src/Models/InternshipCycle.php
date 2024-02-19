<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\InternshipCycle\State;
use DateTimeImmutable;

class InternshipCycle
{
    public function __construct(
        private int $id,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $endedAt,
        private ?DateTimeImmutable $jobCollectionStart,
        private ?DateTimeImmutable $jobCollectionEnd,
        private ?DateTimeImmutable $applyingStart,
        private ?DateTimeImmutable $applyingEnd,
        private ?DateTimeImmutable $interningStart,
        private ?DateTimeImmutable $interningEnd,
        private array $partnerGroupIds,
        private ?int $studentGroupId,
    ) {
    }

    public function getActiveState(): State
    {
        $now = new DateTimeImmutable();
        if ($this->jobCollectionStart <= $now && $now <= $this->jobCollectionEnd) {
            return State::JobCollection;
        }
        if ($this->applyingStart <= $now && $now <= $this->applyingEnd) {
            return State::Applying;
        }
        if ($this->interningStart <= $now && $now <= $this->interningEnd) {
            return State::Interning;
        }
        return State::None;
    }

    public function getNextState(): State
    {
        $activeState = $this->getActiveState();
        if ($activeState === State::JobCollection) {
            return State::Applying;
        }
        if ($activeState === State::Applying) {
            return State::Interning;
        }
        if ($activeState === State::Interning) {
            return State::None;
        }

        $now = new DateTimeImmutable();
        if (!$this->jobCollectionStart || $now <= $this->jobCollectionStart) {
            return State::JobCollection;
        }
        if ($this->jobCollectionEnd <= $now && (!$this->applyingStart || $now <= $this->applyingStart)) {
            return State::Applying;
        }
        if ($this->applyingEnd <= $now && (!$this->interningStart || $now <= $this->interningStart)) {
            return State::Interning;
        }
        return State::None;
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

    public function getApplyingStart(): ?DateTimeImmutable
    {
        return $this->applyingStart;
    }

    public function getApplyingEnd(): ?DateTimeImmutable
    {
        return $this->applyingEnd;
    }

    public function getPartnerGroupIds(): array
    {
        return $this->partnerGroupIds;
    }

    public function getStudentGroupId(): ?int
    {
        return $this->studentGroupId;
    }

    public function setJobCollectionStart(DateTimeImmutable $date): void
    {
        $this->jobCollectionStart = $date;
    }

    public function setJobCollectionEnd(DateTimeImmutable $date): void
    {
        $this->jobCollectionEnd = $date;
    }

    public function setApplyingStart(DateTimeImmutable $date): void
    {
        $this->applyingStart = $date;
    }

    public function setApplyingEnd(DateTimeImmutable $date): void
    {
        $this->applyingEnd = $date;
    }

    public function addPartnerGroupId(int $id): void
    {
        $this->partnerGroupIds[] = $id;
    }

    public function setStudentGroupId(int $id): void
    {
        $this->studentGroupId = $id;
    }
}