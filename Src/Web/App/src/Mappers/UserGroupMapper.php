<?php
declare(strict_types=1);

namespace App\Mappers;

class UserGroupMapper implements \App\Interfaces\IMapper
{
    #[\Override] public static function map(array $row): \App\Models\UserGroup
    {
        return new \App\Models\UserGroup(
            $row["id"],
            $row["name"],
        );
    }
}