<?php
declare(strict_types=1);

namespace App\Security\Policies;

use App\Security\IPolicy;

class JobHuntRoundPolicy implements IPolicy
{
    public function __construct(
        public readonly int $round,
    ) {
    }
}