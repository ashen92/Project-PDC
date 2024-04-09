<?php
declare(strict_types=1);

namespace App\Security\TwigExtension;

use Twig\Extension\AbstractExtension;

class SecurityExtension extends AbstractExtension
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
}