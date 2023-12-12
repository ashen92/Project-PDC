<?php
declare(strict_types=1);

namespace App\Models;

enum RequirementType
{
    case ONE_TIME;
    case RECURRING;

    public static function fromString(string $type): RequirementType
    {
        switch ($type) {
            case "one-time":
                return RequirementType::ONE_TIME;
            case "recurring":
                return RequirementType::RECURRING;
            default:
                throw new \InvalidArgumentException("Invalid requirement type: $type");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            RequirementType::ONE_TIME => "one-time",
            RequirementType::RECURRING => "recurring",
        };
    }
}