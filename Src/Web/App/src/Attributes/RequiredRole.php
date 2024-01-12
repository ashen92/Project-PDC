<?php
declare(strict_types=1);

namespace App\Attributes;

#[\Attribute]
class RequiredRole
{
    /**
     * @param \App\Security\Role|array<\App\Security\Role> $role
     */
    public function __construct(public \App\Security\Role|array $role)
    {

    }
}