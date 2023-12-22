<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Entities\Internship;

class InternshipListViewDTO
{
    public function __construct(
        public Internship $internship,
        public string $organizationName,
        public ?string $organizationLogo = null,
    ) {

    }
}