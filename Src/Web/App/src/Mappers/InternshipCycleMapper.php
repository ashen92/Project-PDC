<?php
declare(strict_types=1);

namespace App\Mappers;

class InternshipCycleMapper implements \App\Interfaces\IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): \App\Models\InternshipCycle
    {
        return new \App\Models\InternshipCycle(
            $row["id"],
            new \DateTimeImmutable($row["createdAt"]),
            $row["endedAt"] === null ? null : new \DateTimeImmutable($row["endedAt"]),
            $row["collectionStartDate"] === null ? null : new \DateTimeImmutable($row["collectionStartDate"]),
            $row["collectionEndDate"] === null ? null : new \DateTimeImmutable($row["collectionEndDate"]),
            $row["applicationStartDate"] === null ? null : new \DateTimeImmutable($row["applicationStartDate"]),
            $row["applicationEndDate"] === null ? null : new \DateTimeImmutable($row["applicationEndDate"]),
            explode(',', $row["partner_group_ids"]),
            $row["student_group_id"],
        );
    }
}