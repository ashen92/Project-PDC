<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum Status
{
    case PENDING;
    case COMPLETED;

    public static function fromString(string $type): Status
    {
        switch ($type) {
            case "pending":
                return Status::PENDING;
            case "completed":
                return Status::COMPLETED;
            default:
                throw new \InvalidArgumentException("Invalid requirement status: $type");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            Status::PENDING => "pending",
            Status::COMPLETED => "completed",
        };
    }
}