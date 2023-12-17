<?php
declare(strict_types=1);

namespace App\DTOs;

class InternshipDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $ownerId,
    ) {
    }
}