<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;

interface IAuthenticationService
{
    public function login(string $username, string $password): bool;
    public function logout(): void;
    public function isAuthenticated(): bool;
}