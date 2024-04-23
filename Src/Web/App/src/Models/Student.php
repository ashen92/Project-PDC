<?php
declare(strict_types=1);

namespace App\Models;

class Student extends User implements \JsonSerializable
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
        string $type,
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
            $type,
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

    /**
     * Specify data which should be serialized to JSON
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource .
     */
    function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), get_object_vars($this));
    }
}