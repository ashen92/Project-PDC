<?php
declare(strict_types=1);

namespace App\Models\Requirement;

enum FulFillMethod: string
{
    case FILE_UPLOAD = 'file-upload';
    case TEXT_INPUT = 'text-input';
}