<?php
declare(strict_types=1);

namespace App\Models;

class InternshipCycle
{
    public function __construct(
        private int $id,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $endedAt,
        private ?\DateTimeImmutable $collectionStartDate,
        private ?\DateTimeImmutable $collectionEndDate,
        private ?\DateTimeImmutable $applicationStartDate,
        private ?\DateTimeImmutable $applicationEndDate,
        private ?int $partnerGroupId,
        private ?int $studentGroupId,
    ) {
    }

    public function end(): void
    {
        $this->endedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getCollectionStartDate(): ?\DateTimeImmutable
    {
        return $this->collectionStartDate;
    }

    public function getCollectionEndDate(): ?\DateTimeImmutable
    {
        return $this->collectionEndDate;
    }

    public function getApplicationStartDate(): ?\DateTimeImmutable
    {
        return $this->applicationStartDate;
    }

    public function getApplicationEndDate(): ?\DateTimeImmutable
    {
        return $this->applicationEndDate;
    }

    public function getPartnerGroupId(): ?int
    {
        return $this->partnerGroupId;
    }

    public function getStudentGroupId(): ?int
    {
        return $this->studentGroupId;
    }

    public function setCollectionStartDate(\DateTimeImmutable $collectionStartDate): void
    {
        $this->collectionStartDate = $collectionStartDate;
    }

    public function setCollectionEndDate(\DateTimeImmutable $collectionEndDate): void
    {
        $this->collectionEndDate = $collectionEndDate;
    }

    public function setApplicationStartDate(\DateTimeImmutable $applicationStartDate): void
    {
        $this->applicationStartDate = $applicationStartDate;
    }

    public function setApplicationEndDate(\DateTimeImmutable $applicationEndDate): void
    {
        $this->applicationEndDate = $applicationEndDate;
    }

    public function setPartnerGroupId(int $partnerGroupId): void
    {
        $this->partnerGroupId = $partnerGroupId;
    }

    public function setStudentGroupId(int $studentGroupId): void
    {
        $this->studentGroupId = $studentGroupId;
    }
}