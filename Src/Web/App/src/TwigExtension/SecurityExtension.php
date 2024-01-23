<?php
declare(strict_types=1);

namespace App\TwigExtension;

class SecurityExtension extends \Twig\Extension\AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction(
                'has_permission',
                ['App\TwigExtension\SecurityRuntimeExtension', 'hasPermission']
            ),
        ];
    }
}