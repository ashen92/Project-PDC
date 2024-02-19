<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Application;

use App\Models\Application\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusType extends \Doctrine\DBAL\Types\Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return "ENUM('pending', 'hired', 'rejected')";
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
        return 'application_status';
    }
}