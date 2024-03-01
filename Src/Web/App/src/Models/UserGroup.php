<?php
declare(strict_types=1);

namespace App\Models;

class UserGroup
{
    public const AUTO_GENERATED_USER_GROUP_PREFIX = 'SystemGenerated-';

    public function __construct(
        private int $id,
        private string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}