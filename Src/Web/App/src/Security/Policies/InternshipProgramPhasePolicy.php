<?php
declare(strict_types=1);

namespace App\Security\Policies;

use App\Security\IPolicy;

class InternshipProgramPhasePolicy implements IPolicy
{
    public const JobCollectionPhase = 'JobCollectionPhase';
    public const FirstRoundPhase = 'FirstRoundPhase';
    public const SecondRoundPhase = 'SecondRoundPhase';

    public readonly string $phase;

    public function __construct(string $phase)
    {
        if (!in_array($phase, [self::JobCollectionPhase, self::FirstRoundPhase, self::SecondRoundPhase])) {
            throw new \InvalidArgumentException("Invalid phase: $phase");
        }

        $this->phase = $phase;
    }
}