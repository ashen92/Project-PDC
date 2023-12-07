<?php
declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute]
class RequiredRole
{
    public function __construct(public string|array $role)
    {

    }
}