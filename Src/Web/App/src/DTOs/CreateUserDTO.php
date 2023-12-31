<?php

namespace App\DTOs;

class CreateUserDTO
{
    public function __construct(
        public readonly string $userType,
        public readonly ?string $email,
        public readonly ?string $firstName,
        public readonly ?string $studentEmail,
        public readonly ?string $sendEmail,
        public readonly ?string $fullName,
        public readonly ?string $registrationNumber,
        public readonly ?string $indexNumber,
        public readonly ?string $organization,
    ) {
    }
}