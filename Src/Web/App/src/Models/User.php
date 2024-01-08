<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class User
{
    public function __construct(
        private int $id,
        private ?string $email = null,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $passwordHash = null,
        private bool $isActive = false,
        private ?string $activationToken = null,
        private ?DateTime $activationTokenExpiresAt = null,
    ) {
    }

    public function generateActivationToken(): string
    {
        $this->activationToken = bin2hex(random_bytes(32));
        $this->activationTokenExpiresAt = new DateTime("+1 day");
        return $this->activationToken;
    }

    public function resetActivationToken(): void
    {
        $this->activationToken = null;
        $this->activationTokenExpiresAt = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function getActivationTokenExpiresAt(): ?DateTime
    {
        return $this->activationTokenExpiresAt;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}