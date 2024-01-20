<?php
declare(strict_types=1);

namespace App\Security;

class Identity
{
    /**
     * @param array<Role> $roles
     */
    public function __construct(private array $roles)
    {
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles);
    }
}