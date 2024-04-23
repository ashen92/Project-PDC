<?php
declare(strict_types=1);

namespace App\Security\Attributes;

#[\Attribute]
class RequiredPolicy
{
    public function __construct(
        public string $policyName
    ) {

    }
}