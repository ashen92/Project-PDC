<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IAuthenticationService
{
    public function login(string $email, string $password): ?\App\Models\User;
}