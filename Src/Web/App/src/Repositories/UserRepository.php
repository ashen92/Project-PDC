<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\IDatabaseConnection;
use App\Models\User;

class UserRepository
{
    public function __construct(private IDatabaseConnection $dbConnection)
    {

    }

    public function findUserByEmail($email): User|null
    {

        // Query the database to find a user by email
        return new User("mail@mail.com", "Ashen", "12345");
    }

    public function findUserById($id): User|null
    {

        // Query the database to find a user by ID
        return new User("mail@mail.com", "Ashen", "12345");
    }

    public function saveUser(User $user)
    {
        // Save the user object to the database
    }
}