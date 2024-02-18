<?php
declare(strict_types=1);

namespace DB\DoctrineTypes\Requirement;

use App\Models\Requirement\FulFillMethod;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class FulFillMethodType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('file-upload', 'text-input')";
    }
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? FulFillMethod::tryFrom($value) : null;
    }

    /**
     * @param FulfillMethod|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? $value->value : null;
    }
    public function getName()
    {
        return "requirement_fulfill_method";
    }
}