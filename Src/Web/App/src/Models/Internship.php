<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Internship\Status;

class Internship
{
    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private Status $status,
        private int $createdByUserId,
        private int $organizationId,
        private int $internshipCycleId,
        private \DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedByUserId(): int
    {
        return $this->createdByUserId;
    }

    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }

    public function getInternshipCycleId(): int
    {
        return $this->internshipCycleId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}