<?php
declare(strict_types=1);

namespace App\Mappers;

class InternshipCycleMapper implements \App\Interfaces\IMapper
{
    #[\Override] public static function map(array $data): \App\Models\InternshipCycle
    {
        return new \App\Models\InternshipCycle(
            $data["id"],
            new \DateTimeImmutable($data["createdAt"]),
            $data["endedAt"] === null ? null : new \DateTimeImmutable($data["endedAt"]),
            $data["collectionStartDate"] === null ? null : new \DateTimeImmutable($data["collectionStartDate"]),
            $data["collectionEndDate"] === null ? null : new \DateTimeImmutable($data["collectionEndDate"]),
            $data["applicationStartDate"] === null ? null : new \DateTimeImmutable($data["applicationStartDate"]),
            $data["applicationEndDate"] === null ? null : new \DateTimeImmutable($data["applicationEndDate"]),
            $data["student_group_id"],
            $data["partner_group_id"],
        );
    }
}