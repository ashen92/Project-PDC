<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;

$container = new ContainerBuilder();

$container->register(
    "request.stack",
    RequestStack::class
);

// Services
require_once __DIR__ . "/container.services.php";

// Controllers
$container->register(
    "App\Controllers\AuthenticationController",
    App\Controllers\AuthenticationController::class
)
    ->setArguments([new Reference("service.authentication"), new Reference("service.user")]);

$container->register(
    "App\Controllers\PageControllers\AuthenticationPageController",
    App\Controllers\PageControllers\AuthenticationPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig"), new Reference("service.authentication")]);

$container->register(
    "App\Controllers\PageControllers\HomePageController",
    App\Controllers\PageControllers\HomePageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig")]);


$container->register(
    "App\Controllers\PageControllers\TechTalksPageController",
    App\Controllers\PageControllers\TechTalksPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig")]);

$container->register(
    "App\Controllers\PageControllers\InternshipPageController",
    App\Controllers\PageControllers\InternshipPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig")]);

return $container;