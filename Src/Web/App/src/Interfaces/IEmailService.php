<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Email;

interface IEmailService
{
    public function sendEmail(Email $email): bool;
}