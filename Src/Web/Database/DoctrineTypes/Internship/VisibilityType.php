<?php
declare(strict_types=1);

namespace DB\DoctrineTypes\Internship;

use App\Models\Internship\Visibility;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class VisibilityType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('private', 'public')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? Visibility::tryFrom($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value?->value;
    }

    public function getName(): string
    {
        return 'internship_visibility';
    }
}