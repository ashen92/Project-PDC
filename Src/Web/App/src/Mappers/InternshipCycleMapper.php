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
            $row['jobHuntRound1Start'] === null ? null : new DateTimeImmutable($row['jobHuntRound1Start']),
            $row['jobHuntRound1End'] === null ? null : new DateTimeImmutable($row['jobHuntRound1End']),
            $row['jobHuntRound2Start'] === null ? null : new DateTimeImmutable($row['jobHuntRound2Start']),
            $row['jobHuntRound2End'] === null ? null : new DateTimeImmutable($row['jobHuntRound2End']),
            explode(',', $row['partner_group_ids']),
            $row['student_group_id'],
        );
    }
}