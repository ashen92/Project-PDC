<?php
declare(strict_types=1);

namespace App\DoctrineTypes;

use App\Models\RequirementType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class RequirementTypeType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('one-time', 'recurring')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return RequirementType::fromString($value);
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