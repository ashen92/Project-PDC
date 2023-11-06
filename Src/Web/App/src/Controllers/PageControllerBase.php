<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class PageControllerBase
{
    public function __construct(private Environment $twig)
    {

    }

    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

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