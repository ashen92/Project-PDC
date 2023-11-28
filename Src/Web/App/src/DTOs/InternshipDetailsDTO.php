<?php
declare(strict_types=1);

namespace App\DTOs;

class InternshipDetailsDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
    ) {
    }
}