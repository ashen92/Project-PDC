<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthorizationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class PageControllerBase
{
    public function __construct(private AuthorizationService $authz, private Environment $twig)
    {

    }

    abstract protected function getSectionName(): string;
    abstract protected function getSectionURL(): string;

    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    protected function render(string $template): Response
    {
        return new Response(
            $this->twig->render(
                $template,
                [
                    "sectionName" => $this->getSectionName(),
                    "sectionURL" => $this->getSectionURL()
                ]
            )
        );
    }
}