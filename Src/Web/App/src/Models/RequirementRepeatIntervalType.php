<?php
declare(strict_types=1);

namespace App\Models;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class RequirementRepeatIntervalType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('daily', 'weekly', 'monthly')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? RequirementRepeatInterval::fromString($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? $value->toString() : null;
    }

    public function getName()
    {
        return "requirement_repeat_interval";
    }
}