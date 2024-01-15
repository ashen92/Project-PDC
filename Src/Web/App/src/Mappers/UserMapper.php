<?php
declare(strict_types=1);

namespace App\Mappers;

class UserMapper implements \App\Interfaces\IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): \App\Models\User
    {
        return new \App\Models\User(
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