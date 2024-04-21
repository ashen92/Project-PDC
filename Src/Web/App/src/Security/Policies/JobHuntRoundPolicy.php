<?php
declare(strict_types=1);

namespace App\Security\Policies;

use App\Security\IPolicy;

class JobHuntRoundPolicy implements IPolicy
{
    public const FirstRound = 1;
    public const SecondRound = 2;

    public readonly int $round;

    public function __construct(int $round)
    {
        if ($round !== self::FirstRound && $round !== self::SecondRound) {
            throw new \InvalidArgumentException('Invalid job hunt round');
        }
        $this->round = $round;
    }
}