<?php
declare(strict_types=1);

namespace App\Models;
use App\Interfaces\IDatabaseConnection;

class UserRepository
{
    public function __construct( private IDatabaseConnection $dbConnection)
    {
        
    }
}