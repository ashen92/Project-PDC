<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Security\AuthorizationService;
use App\Security\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class ControllerBase
{
    public function __construct(
        private readonly Environment $twig,
        private readonly AuthorizationService $authzService
    ) {

    }

    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    protected function hasRole(Role $role): bool
    {
        return $this->authzService->hasRole($role);
    }

    /**
     * @param array<mixed> $parameters
     */
    protected function render(string $template, array $parameters = [], int $responseStatus = 200): Response
    {
        return new Response(
            $this->twig->render(
                $template,
                $parameters
            ),
            $responseStatus
        );
    }
}