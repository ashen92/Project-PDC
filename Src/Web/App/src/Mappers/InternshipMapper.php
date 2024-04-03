<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\Internship;
use App\Models\Internship\Visibility;
use DateTimeImmutable;
use Exception;
use Override;

class InternshipMapper implements IMapper
{
    /**
     * @throws Exception
     */
    #[Override] public static function map(array $row): Internship
    {
        return new Internship(
            $row['id'],
            $row['title'],
            $row['description'],
            Visibility::tryFrom($row['visibility']),
            $row['created_by_user_id'],
            $row['organization_id'],
            $row['internship_cycle_id'],
            new DateTimeImmutable($row['createdAt']),
        );
    }
}