<?php
declare(strict_types=1);

namespace App\Mappers;

class UserMapper implements \App\Interfaces\IMapper
{
    #[\Override] public static function map(array $data): \App\Models\User
    {
        return new \App\Models\User(
            $data["id"],
            $data["email"],
            $data["firstName"],
            $data["lastName"],
            $data["passwordHash"],
            $data["isActive"] === 1,
            $data["activationToken"],
            $data["activationTokenExpiresAt"] === null ? null : new \DateTimeImmutable($data["activationTokenExpiresAt"]),
        );
    }
}