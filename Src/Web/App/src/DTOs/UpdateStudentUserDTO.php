<?php
declare(strict_types=1);

namespace App\DTOs;

class UpdateStudentUserDTO
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $password,
        public ?string $confirmPassword,
    ) {
    }
}