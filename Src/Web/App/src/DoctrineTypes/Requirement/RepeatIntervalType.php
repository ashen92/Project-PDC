<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Requirement;

use App\Models\Requirement\RepeatInterval;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class RepeatIntervalType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('daily', 'weekly', 'monthly')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? RepeatInterval::fromString($value) : null;
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