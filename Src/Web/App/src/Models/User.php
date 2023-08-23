<?php
declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        private string $email,
        private string $firstname,
        private string $passwordHash
    ) {

    }
}