<?php
declare(strict_types=1);

namespace App\Models;

class User
{
    private const ACTIVATION_TOKEN_VALID_DURATION = "PT1H";

    public function __construct(
        private int $id,
        private ?string $email,
        private ?string $firstName,
        private ?string $lastName,
        private ?string $passwordHash,
        private bool $isActive,
        private ?string $activationToken,
        private ?\DateTimeImmutable $activationTokenExpiresAt,
        private string $type,
    ) {
    }

    public function generateActivationToken(): string
    {
        $this->activationToken = bin2hex(random_bytes(32));

        $now = new \DateTimeImmutable("now");
        $this->activationTokenExpiresAt = $now->add(new \DateInterval(self::ACTIVATION_TOKEN_VALID_DURATION));

        return $this->activationToken;
    }

    public function resetActivationToken(): void
    {
        $this->activationToken = null;
        $this->activationTokenExpiresAt = null;
    }

    public function activate(): void
    {
        $this->isActive = true;
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

    public function getActivationTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->activationTokenExpiresAt;
    }

    public function getType(): string
    {
        return $this->type;
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
}