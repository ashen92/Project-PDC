<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum FulFillMethod
{
    case FILE_UPLOAD;
    case TEXT_INPUT;

    public static function fromString(string $type): FulFillMethod
    {
        switch ($type) {
            case "file-upload":
                return FulFillMethod::FILE_UPLOAD;
            case "text-input":
                return FulFillMethod::TEXT_INPUT;
            default:
                throw new \InvalidArgumentException("Invalid requirement fulfill method: $type");
        }
    }

    public function toString(): string
    {
        return match ($this) {
            FulFillMethod::FILE_UPLOAD => "file-upload",
            FulFillMethod::TEXT_INPUT => "text-input",
        };
    }
}