<?php
declare(strict_types=1);

namespace App\Models;

enum RequirementStatus
{
    case PENDING;
    case COMPLETED;

    public static function fromString(string $type): RequirementStatus
    {
        switch ($type) {
            case "pending":
                return RequirementStatus::PENDING;
            case "completed":
                return RequirementStatus::COMPLETED;
            default:
                throw new \InvalidArgumentException("Invalid requirement status: $type");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            RequirementStatus::PENDING => "pending",
            RequirementStatus::COMPLETED => "completed",
        };
    }
}