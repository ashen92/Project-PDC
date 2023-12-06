<?php
declare(strict_types=1);

namespace App\DTOs;

class StudentUserViewDTO extends UserViewDTO {

    public function __construct(
        public string $studentEmail,
        public string $fullName,
        int $id,
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?string $passwordHash,
        bool $isActive,
        ?string $activationToken,
        ?\DateTime $activationTokenExpiresAt,
    ) {
        parent::__construct($id, $email, $firstName, $lastName, $passwordHash, $isActive, $activationToken, $activationTokenExpiresAt);
    }
}