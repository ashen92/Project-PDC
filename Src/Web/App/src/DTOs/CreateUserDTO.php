<?php

namespace App\DTOs;

readonly class CreateUserDTO
{
    public function __construct(
        public string  $userType,
        public ?string $email = null,
        public ?string $firstName = null,
        public ?string $studentEmail = null,
        public ?string $sendEmail = null,
        public ?string $fullName = null,
        public ?string $registrationNumber = null,
        public ?string $indexNumber = null,
        public ?int    $organizationId = null,
    ) {
    }
}