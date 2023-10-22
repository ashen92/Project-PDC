<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IAuthenticationService
{
    public function authenticate(string $username, string $passwordHash): bool;
    public function logout(): void;
}