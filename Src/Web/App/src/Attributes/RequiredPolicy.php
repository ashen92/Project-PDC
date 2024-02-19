<?php
declare(strict_types=1);

namespace App\Attributes;

#[\Attribute]
class RequiredPolicy
{
    public function __construct(
        public mixed $policy
    ) {

    }
}