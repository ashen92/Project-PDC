<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IDatabaseConnection;
use App\Entities\User;

class UserRepository
{
    public function __construct(private IDatabaseConnection $dbConnection)
    {

    }

    public function findUserByEmail($email): User|null
    {
        // Query the database to find a user by email
        // todo

        if ($email == "admin@mail.com") {
            return new User("admin@mail.com", "Ashen", "12345", ["admin"]);
        }
        if ($email == "pdc@mail.com") {
            return new User("pdc@mail.com", "Ashen", "12345", ["admin"]);
        }
        if ($email == "partner@mail.com") {
            return new User("partner@mail.com", "Ashen", "12345", ["partner"]);
        }
        return new User("user@mail.com", "Ashen", "12345", ["user"]);
    }

    public function findUserById($id): User|null
    {

        // Query the database to find a user by ID
        return null;
    }

    public function saveUser(User $user)
    {
        // Save the user object to the database
    }
}