<?php
declare(strict_types=1);

namespace App\Security;

enum Role: string
{
    case InternshipProgram_Admin = "ROLE_INTERNSHIP_ADMIN";
    case InternshipProgram_Partner_Admin = "ROLE_INTERNSHIP_PARTNER";
    case InternshipProgram_Partner = "ROLE_INTERNSHIP_MANAGED_PARTNER";
    case InternshipProgram_Student = "ROLE_INTERNSHIP_STUDENT";

    case Admin = "ROLE_ADMIN";
}