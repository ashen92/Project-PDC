<?php
declare(strict_types=1);

namespace App\DTOs;

class CreateStudentUserDTO {
    public function __construct(
        public int $id,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $password,
        public ?string $confirmPassword,
    ) {
    }
}