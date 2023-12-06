<?php
declare(strict_types=1);

namespace App\DTOs;

class UserViewDTO {
    public function __construct(
        public int $id,
        public ?string $email,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $passwordHash,
        public bool $isActive,
        public ?string $activationToken,
        public ?\DateTime $activationTokenExpiresAt,
    ) {
    }
}