<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Models\Internship\Visibility;

readonly class createInternshipDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $createdByUserId,
        public ?int $organizationId,
        public Visibility $visibility = Visibility::Private ,
        public bool $isApproved = false,
    ) {
    }
}