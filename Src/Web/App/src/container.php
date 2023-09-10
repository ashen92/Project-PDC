<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

$container = new ContainerBuilder();

$container->register(
    "request.stack",
    RequestStack::class
);

$container->register(
    "app.cache",
    ApcuAdapter::class
)
    ->setArguments(["app"]);

// Services
require_once __DIR__ . "/container.services.php";

// Controllers

$container->register(
    "App\Controllers\AuthenticationController",
    \App\Controllers\AuthenticationController::class
)
    ->setArguments([new Reference("twig"), new Reference("service.authentication")]);

$container->register(
    "App\Controllers\HomeController",
    \App\Controllers\HomeController::class
)
    ->setArguments([new Reference("twig")]);


$container->register(
    "App\Controllers\TechTalksController",
    \App\Controllers\TechTalksController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\InternshipController",
    \App\Controllers\InternshipController::class
)
    ->setArguments([new Reference("twig")]);

return $container;