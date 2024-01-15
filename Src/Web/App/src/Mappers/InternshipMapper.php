<?php
declare(strict_types=1);

namespace App\Mappers;

class InternshipMapper implements \App\Interfaces\IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): \App\Models\Internship
    {
        return new \App\Models\Internship(
            $row["id"],
            $row["title"],
            $row["description"],
            $row["owner_user_id"],
            $row["organization_id"],
            $row["internship_cycle_id"],
            new \DateTimeImmutable($row["createdAt"]),
            $row["isPublished"] === 1,
        );
    }
}