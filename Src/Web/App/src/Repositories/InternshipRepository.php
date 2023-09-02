<?php
declare(strict_types=1);

namespace App\Repositories;

class InternshipRepository
{
    public function __construct()
    {
        
    }

    public function getAllInternships()
    {
        return array("Internship 1", "Internship 2", "Internship 3");
    }
}