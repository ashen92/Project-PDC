<?php
declare(strict_types=1);

namespace App\Models\Internship;

enum Visibility: string
{
    case Private = 'private';
    case Public = 'public';
}