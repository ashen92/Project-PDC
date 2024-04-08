<?php
declare(strict_types=1);

namespace App\Security\TwigExtension;

class SecurityRuntimeLoader implements \Twig\RuntimeLoader\RuntimeLoaderInterface
{
    public function __construct(
        private readonly SecurityRuntimeExtension $securityRuntimeExtension
    ) {
    }

    public function load(string $class): ?object
    {
        if ($class === 'App\Security\TwigExtension\SecurityRuntimeExtension') {
            return $this->securityRuntimeExtension;
        }
        return null;
    }
}