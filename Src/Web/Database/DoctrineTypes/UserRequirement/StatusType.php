<?php
declare(strict_types=1);

namespace DB\DoctrineTypes\UserRequirement;

use App\Models\UserRequirement\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('pending', 'fulfilled')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? Status::tryFrom($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value?->value;
    }

    public function getName(): string
    {
        return 'user_requirement_status';
    }
}