<?php
declare(strict_types=1);

namespace App\Mappers;

class StudentMapper implements \App\Interfaces\IMapper
{
    public static function map(array $data): \App\Models\Student
    {
        return new \App\Models\Student(
            $data["studentEmail"],
            $data["fullName"],
            $data["registrationNumber"],
            $data["indexNumber"],
            $data["id"],
            $data["email"],
            $data["firstName"],
            $data["lastName"],
            $data["passwordHash"],
            $data["isActive"] === 1,
            $data["activationToken"],
            $data["activationTokenExpiresAt"],
        );
    }
}