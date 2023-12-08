<?php
declare(strict_types=1);

namespace App\DTOs;

class InternshipStudentUserViewDTO
{
    public function __construct(
        public int $id,
        public string $studentEmail,
        public string $fullName,
        public string $indexNumber,
        public string $firstName,
    ) {
    }
}