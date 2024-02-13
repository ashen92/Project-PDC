<?php
declare(strict_types=1);

namespace App\Models\Internship;

enum Status: string
{
    case Draft = 'draft';
    case Private = 'private';
    case Public = 'public';
}