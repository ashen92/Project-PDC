<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\User;

interface IAuthenticationService
{
    public function login(string $email, string $password): ?User;
}