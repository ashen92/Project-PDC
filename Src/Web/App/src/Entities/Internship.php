<?php
declare(strict_types=1);

namespace App\Entities;

class Internship
{
    public function __construct(public int $id, public string $description, public string $company)
    {
        
    }
}