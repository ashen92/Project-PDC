<?php
declare(strict_types=1);

namespace App\Security\Attributes;

#[\Attribute]
class RequiredAtLeastOne
{
    /**
     * @param array<string> $roles
     * @param array<string> $policies
     */
    public function __construct(
        public array $roles,
        public array $policies
    ) {

    }
}