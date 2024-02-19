<?php
declare(strict_types=1);

namespace App\Models\UserRequirement;

enum Status: string
{
    case PENDING = 'pending';
    case FULFILLED = 'fulfilled';
}