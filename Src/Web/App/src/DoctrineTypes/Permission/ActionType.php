<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Permission;

use App\Security\Permission\Action;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ActionType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('create', 'read', 'update', 'delete')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Action::tryFrom($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->value;
    }

    public function getName()
    {
        return "permission_action";
    }
}