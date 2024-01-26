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
        private array $partnerGroupIds,
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

    public function getPartnerGroupIds(): array
    {
        return $this->partnerGroupIds;
    }

    public function getStudentGroupId(): ?int
    {
        return $this->studentGroupId;
    }

    public function setCollectionStartDate(\DateTimeImmutable $date): void
    {
        $this->collectionStartDate = $date;
    }

    public function setCollectionEndDate(\DateTimeImmutable $date): void
    {
        $this->collectionEndDate = $date;
    }

    public function setApplicationStartDate(\DateTimeImmutable $date): void
    {
        $this->applicationStartDate = $date;
    }

    public function setApplicationEndDate(\DateTimeImmutable $date): void
    {
        $this->applicationEndDate = $date;
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