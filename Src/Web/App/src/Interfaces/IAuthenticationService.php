<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;

interface IAuthenticationService
{
    public function login(string $username, string $password): User|null;
    public function logout(): void;
}