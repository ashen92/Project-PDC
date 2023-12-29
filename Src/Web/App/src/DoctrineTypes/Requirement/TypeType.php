<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Requirement;

use App\Models\Requirement;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TypeType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('one-time', 'recurring')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Requirement\Type::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->toString();
    }

    public function getName()
    {
        return "requirement_type";
    }
}