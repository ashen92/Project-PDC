<?php
declare(strict_types=1);

namespace App\Security\Attributes;

use App\Security\Role;

#[\Attribute]
class RequiredRole
{
    /**
     * @param Role|array<Role> $role
     */
    public function __construct(public Role|array $role)
    {

    }
}