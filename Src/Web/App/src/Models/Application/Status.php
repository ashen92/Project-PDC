<?php
declare(strict_types=1);

namespace App\Models\Application;

enum Status: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}