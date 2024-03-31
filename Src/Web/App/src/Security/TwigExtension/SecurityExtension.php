<?php
declare(strict_types=1);

namespace App\Security\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SecurityExtension extends AbstractExtension implements GlobalsInterface
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction(
                'has_permission',
                ['App\Security\TwigExtension\SecurityRuntimeExtension', 'hasPermission']
            ),
            new \Twig\TwigFunction(
                'has_role',
                ['App\Security\TwigExtension\SecurityRuntimeExtension', 'hasRole']
            ),
            new \Twig\TwigFunction(
                'IsAuthorized',
                ['App\Security\TwigExtension\SecurityRuntimeExtension', 'isAuthorized']
            ),
        ];
    }

    public function getGlobals(): array
    {
        return [
            'InternshipProgramAdmin' => \App\Security\Role::InternshipProgramAdmin,
            'InternshipProgramPartnerAdmin' => \App\Security\Role::InternshipProgramPartnerAdmin,
            'InternshipProgramPartner' => \App\Security\Role::InternshipProgramPartner,
            'InternshipProgramStudent' => \App\Security\Role::InternshipProgramStudent,
            'Admin' => \App\Security\Role::Admin,
        ];
    }
}