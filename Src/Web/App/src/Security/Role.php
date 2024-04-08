<?php
declare(strict_types=1);

namespace App\Security;

enum Role: string
{
    case InternshipProgramAdmin = 'internship_program_admin';
    case InternshipProgramPartnerAdmin = 'internship_program_partner_admin';
    case InternshipProgramPartner = 'internship_program_partner';
    case InternshipProgramStudent = 'internship_program_student';

    case Admin = 'admin';
}