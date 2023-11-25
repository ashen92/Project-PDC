<?php

namespace App\Interfaces;

interface IPasswordHasher
{
    public function hashPassword(string $password): string;
    public function verifyPassword(string $password, string $hash): bool;
}