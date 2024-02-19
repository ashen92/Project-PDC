<?php
declare(strict_types=1);

namespace App\Mappers;

class StudentMapper implements \App\Interfaces\IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): \App\Models\Student
    {
        return new \App\Models\Student(
            $row["studentEmail"],
            $row["fullName"],
            $row["registrationNumber"],
            $row["indexNumber"],
            $row["id"],
            $row["email"],
            $row["firstName"],
            $row["lastName"],
            $row["passwordHash"],
            $row["isActive"] === 1,
            $row["activationToken"],
            $row["activationTokenExpiresAt"] === null ? null : new \DateTimeImmutable($row["activationTokenExpiresAt"]),
            $row["type"],
        );
    }
}