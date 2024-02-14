<?php
declare(strict_types=1);

namespace App\Models\InternshipCycle;

enum State
{
    case JobCollection;
    case Applying;
    case Interning;
    case None;
    case Started;
    case Ended;
}