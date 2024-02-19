<?php

namespace App\Models\User;

enum Type: string
{
    case USER = 'user';
    case STUDENT = 'student';
    case PARTNER = 'partner';
}