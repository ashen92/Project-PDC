<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum Type: string
{
    case ONE_TIME = 'one-time';
    case RECURRING = 'recurring';
}