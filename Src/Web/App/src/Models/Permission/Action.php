<?php
declare(strict_types=1);

namespace App\Models\Permission;

enum Action: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
}