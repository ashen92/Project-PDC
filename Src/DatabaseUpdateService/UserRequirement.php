<?php
declare(strict_types=1);

namespace DatabaseUpdateService;

use DateTimeImmutable;

class UserRequirement
{
    public const STATUS_PENDING = 'pending';

    public function __construct(
        private int $id,
        private int $userId,
        private int $requirementId,
        private string $fulfillMethod,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
        private ?DateTimeImmutable $completedAt,
        private string $status,
        private ?string $textResponse,
    ) {
    }
}