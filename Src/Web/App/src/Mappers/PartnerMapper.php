<?php

namespace App\Mappers;

use App\Models\Partner;

class PartnerMapper implements \App\Interfaces\IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): Partner
    {
        return new Partner(
            $row['organization_id'],
            $row['managedBy_id'],
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