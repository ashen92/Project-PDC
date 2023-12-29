<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum Type
{
    case ONE_TIME;
    case RECURRING;

    public static function fromString(string $type): Type
    {
        switch ($type) {
            case "one-time":
                return Type::ONE_TIME;
            case "recurring":
                return Type::RECURRING;
            default:
                throw new \InvalidArgumentException("Invalid requirement type: $type");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            Type::ONE_TIME => "one-time",
            Type::RECURRING => "recurring",
        };
    }
}