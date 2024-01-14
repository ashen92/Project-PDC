<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;

class OrganizationMapper implements IMapper
{
    #[\Override] public static function map(array $row): mixed
    {
        return new \App\Models\Organization(
            $row['id'],
            $row['name'],
            $row['address'],
            $row['city'],
            $row['industry'],
            $row['website'],
            $row['tagline'],
            $row['logoFilePath']
        );
    }
}