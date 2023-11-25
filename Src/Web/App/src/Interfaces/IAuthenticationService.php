<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IAuthenticationService
{
    public function authenticate(string $email, string $password): bool;
    public function logout(): void;
}