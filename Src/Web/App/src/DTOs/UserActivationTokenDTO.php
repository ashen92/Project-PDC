<?php
declare(strict_types=1);

namespace App\DTOs;

class UserActivationTokenDTO {
    public function __construct(
        public int $id,
        public ?string $activationToken = null,
        public ?\DateTime $activationTokenExpiresAt = null,
    ) {
    }
}