<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum RepeatInterval: string
{
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function toDuration(): string
    {
        return match ($this) {
            RepeatInterval::WEEKLY => 'P1W',
            RepeatInterval::MONTHLY => 'P1M',
        };
    }
}