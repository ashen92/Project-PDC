<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IAuthenticationService;
use App\Models\User;

class DBAuthenticationService implements IAuthenticationService
{
    public function authenticate(string $email, string $password): User|null
    {
        // authenticate against database. use UserRepository
        // todo

        return new User("mail@mail.com", "Ashen", "12345");
    }

    public function logout(): void
    {

    }
}