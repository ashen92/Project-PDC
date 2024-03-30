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
        ];
    }

    public function getGlobals(): array
    {
        return [
            'InternshipProgram_Admin' => \App\Security\Role::InternshipProgram_Admin,
            'InternshipProgram_Partner_Admin' => \App\Security\Role::InternshipProgram_Partner_Admin,
            'InternshipProgram_Partner' => \App\Security\Role::InternshipProgram_Partner,
            'InternshipProgram_Student' => \App\Security\Role::InternshipProgram_Student,
            'Admin' => \App\Security\Role::Admin,
        ];
    }
}