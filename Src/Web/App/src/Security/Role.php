<?php
declare(strict_types=1);

namespace App\Security;

enum Role: string
{
    case InternshipProgram_Admin = 'internship_program_admin';
    case InternshipProgram_Partner_Admin = 'internship_program_partner_admin';
    case InternshipProgram_Partner = 'internship_program_partner';
    case InternshipProgram_Student = 'internship_program_student';

    case Admin = 'admin';
}