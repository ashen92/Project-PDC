<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum RepeatInterval
{
    case DAILY;
    case WEEKLY;
    case MONTHLY;

    public static function fromString(string $interval): RepeatInterval
    {
        switch ($interval) {
            case "daily":
                return RepeatInterval::DAILY;
            case "weekly":
                return RepeatInterval::WEEKLY;
            case "monthly":
                return RepeatInterval::MONTHLY;
            default:
                throw new \InvalidArgumentException("Invalid requirement repeat interval: $interval");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            RepeatInterval::DAILY => "daily",
            RepeatInterval::WEEKLY => "weekly",
            RepeatInterval::MONTHLY => "monthly",
        };
    }

    public function toDuration(): string
    {
        return match ($this) {
            RepeatInterval::DAILY => "P1D",
            RepeatInterval::WEEKLY => "P1W",
            RepeatInterval::MONTHLY => "P1M",
        };
    }
}