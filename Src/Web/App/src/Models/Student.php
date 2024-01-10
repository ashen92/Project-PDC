<?php
declare(strict_types=1);

namespace App\Models;

class Student extends User
{
    public function __construct(
        private string $studentEmail,
        private string $fullName,
        private string $registrationNumber,
        private string $indexNumber,
        int $id,
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?string $passwordHash,
        bool $isActive,
        ?string $activationToken,
        ?\DateTimeImmutable $activationTokenExpiresAt,
    ) {
        parent::__construct(
            $id,
            $email,
            $firstName,
            $lastName,
            $passwordHash,
            $isActive,
            $activationToken,
            $activationTokenExpiresAt,
        );
    }

    public function getStudentEmail(): string
    {
        return $this->studentEmail;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getIndexNumber(): string
    {
        return $this->indexNumber;
    }

    public function setStudentEmail(string $studentEmail): void
    {
        $this->studentEmail = $studentEmail;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function setIndexNumber(string $indexNumber): void
    {
        $this->indexNumber = $indexNumber;
    }
}