<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Security\AuthorizationService;
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

    protected function hasRole(string $role): bool
    {
        return $this->authzService->hasRole($role);
    }

    protected function authorize(string $policyName): bool
    {
        return $this->authzService->authorize($policyName);
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

    protected function serveFile(string $file, string $mimeType, string $fileName): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                'inline',
                $fileName
            )
        );
        $response->setContent($file);
        return $response;
    }
}