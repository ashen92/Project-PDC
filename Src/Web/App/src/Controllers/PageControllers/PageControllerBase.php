<?php
declare(strict_types=1);

namespace App\PageControllers\Controllers;

use App\Interfaces\IAuthorizationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class PageControllerBase
{
    public function __construct(private IAuthorizationService $authz, private Environment $twig)
    {

    }

    abstract protected function getSectionName(): string;
    abstract protected function getSectionURL(): string;

    protected function getAuthzService(): IAuthorizationService
    {
        return $this->authz;
    }

    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    protected function render(string $template, array $parameters = []): Response
    {
        return new Response(
            $this->twig->render(
                $template,
                array_merge([
                    "sectionName" => $this->getSectionName(),
                    "sectionURL" => $this->getSectionURL(),
                    "userRoles" => $this->authz->getUserRoles()
                ], $parameters)
            )
        );
    }
}