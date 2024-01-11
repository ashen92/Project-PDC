<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Permission;

use App\Security\Permission\Resource;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ResourceType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('internship')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Resource::tryFrom($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->value;
    }

    public function getName()
    {
        return "permission_resource";
    }
}