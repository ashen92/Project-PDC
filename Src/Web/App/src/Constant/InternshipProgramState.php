<?php
declare(strict_types=1);

namespace App\Constant;

enum InternshipProgramState
{
    case NotStarted;
    case Active;
    case Ended;
    case InternshipCollectionPhase;
    case ApplicationCollectionPhase;
    case InternshipPhase;
}