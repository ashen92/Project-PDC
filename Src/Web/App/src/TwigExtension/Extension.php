<?php
declare(strict_types=1);

namespace App\TwigExtension;

use App\Models\InternshipCycle\State;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Extension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'InternshipCycleState_JobCollection' => State::JobCollection,
            'InternshipCycleState_Applying' => State::Applying,
            'InternshipCycleState_Interning' => State::Interning,
            'InternshipCycleState_None' => State::None,
        ];
    }
}