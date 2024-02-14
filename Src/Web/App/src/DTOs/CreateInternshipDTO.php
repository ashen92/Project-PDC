<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Internship\Status;

readonly class createInternshipDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $createdByUserId,
        public ?int $organizationId,
        public Status $status = Status::Draft,
    ) {
    }
}