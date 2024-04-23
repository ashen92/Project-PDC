<?php
declare(strict_types=1);

namespace DB\DoctrineTypes\Requirement;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class TypeType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('one-time', 'recurring')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? \App\Models\Requirement\Type::tryFrom($value) : null;
    }

    /**
     * @param \App\Models\Requirement\Type|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? $value->value : null;
    }

    public function getName()
    {
        return "requirement_type";
    }
}