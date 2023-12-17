<?php
declare(strict_types=1);

namespace App\Models;

use App\Entities\Internship;

class InternshipView
{
    public function __construct(
        public Internship $internship,
        public string $organizationName,
        public ?string $organizationLogo = null,
    ) {

    }
}