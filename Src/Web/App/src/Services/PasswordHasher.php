<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IPasswordHasher;

class PasswordHasher implements IPasswordHasher
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}