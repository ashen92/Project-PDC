<?php
declare(strict_types=1);

namespace App\Security\Attributes;

#[\Attribute]
class RequiredRole
{
    /**
     * @param string|array<string> $role
     */
    public function __construct(public string|array $role)
    {
    }
}