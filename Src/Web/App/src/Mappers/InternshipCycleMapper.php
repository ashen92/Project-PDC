<?php
declare(strict_types=1);

namespace App\Mappers;

class InternshipCycleMapper implements \App\Interfaces\IMapper
{
    public static function map(array $data): \App\Models\InternshipCycle
    {
        if ($data["endedAt"] !== null) {
            $data["endedAt"] = new \DateTimeImmutable($data["endedAt"]);
        }
        if ($data["collectionStartDate"] !== null) {
            $data["collectionStartDate"] = new \DateTimeImmutable($data["collectionStartDate"]);
        }
        if ($data["collectionEndDate"] !== null) {
            $data["collectionEndDate"] = new \DateTimeImmutable($data["collectionEndDate"]);
        }
        if ($data["applicationStartDate"] !== null) {
            $data["applicationStartDate"] = new \DateTimeImmutable($data["applicationStartDate"]);
        }
        if ($data["applicationEndDate"] !== null) {
            $data["applicationEndDate"] = new \DateTimeImmutable($data["applicationEndDate"]);
        }

        return new \App\Models\InternshipCycle(
            $data["id"],
            new \DateTimeImmutable($data["createdAt"]),
            $data["endedAt"],
            $data["collectionStartDate"],
            $data["collectionEndDate"],
            $data["applicationStartDate"],
            $data["applicationEndDate"],
            $data["student_group_id"],
            $data["partner_group_id"],
        );
    }
}