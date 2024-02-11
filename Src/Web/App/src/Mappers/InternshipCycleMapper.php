<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\InternshipCycle;
use DateTimeImmutable;
use Exception;

class InternshipCycleMapper implements IMapper
{
    /**
     * @throws Exception
     */
    #[\Override] public static function map(array $row): InternshipCycle
    {
        return new InternshipCycle(
            $row['id'],
            new DateTimeImmutable($row['createdAt']),
            $row['endedAt'] === null ? null : new DateTimeImmutable($row['endedAt']),
            $row['jobCollectionStart'] === null ? null : new DateTimeImmutable($row['jobCollectionStart']),
            $row['jobCollectionEnd'] === null ? null : new DateTimeImmutable($row['jobCollectionEnd']),
            $row['applyingStart'] === null ? null : new DateTimeImmutable($row['applyingStart']),
            $row['applyingEnd'] === null ? null : new DateTimeImmutable($row['applyingEnd']),
            $row['interningStart'] === null ? null : new DateTimeImmutable($row['interningStart']),
            $row['interningEnd'] === null ? null : new DateTimeImmutable($row['interningEnd']),
            explode(',', $row['partner_group_ids']),
            $row['student_group_id'],
        );
    }
}