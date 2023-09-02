<?php
declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        private string $email,
        private string $firstName,
        private string $passwordHash,
        private array $roles
    ) {

    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}