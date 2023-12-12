<?php
declare(strict_types=1);

namespace App\Models;

enum RequirementRepeatInterval
{
    case DAILY;
    case WEEKLY;
    case MONTHLY;

    public static function fromString(string $interval): RequirementRepeatInterval
    {
        switch ($interval) {
            case "daily":
                return RequirementRepeatInterval::DAILY;
            case "weekly":
                return RequirementRepeatInterval::WEEKLY;
            case "monthly":
                return RequirementRepeatInterval::MONTHLY;
            default:
                throw new \InvalidArgumentException("Invalid requirement repeat interval: $interval");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            RequirementRepeatInterval::DAILY => "daily",
            RequirementRepeatInterval::WEEKLY => "weekly",
            RequirementRepeatInterval::MONTHLY => "monthly",
        };
    }
}