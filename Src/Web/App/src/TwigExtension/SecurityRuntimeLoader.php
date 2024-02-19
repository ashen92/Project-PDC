<?php
declare(strict_types=1);

namespace App\TwigExtension;

class SecurityRuntimeLoader implements \Twig\RuntimeLoader\RuntimeLoaderInterface
{
    public function __construct(
        private readonly SecurityRuntimeExtension $securityRuntimeExtension
    ) {
    }

    public function load(string $class): ?object
    {
        if ($class === 'App\TwigExtension\SecurityRuntimeExtension') {
            return $this->securityRuntimeExtension;
        }
        return null;
    }
}