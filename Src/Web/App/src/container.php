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
include(__DIR__ . "/container.services.php");

// Repositories
include(__DIR__ . "/container.repositories.php");

// Controllers
$container->register(
    "App\Controllers\AuthenticationController",
    App\Controllers\AuthenticationController::class
)
    ->setArguments([new Reference("service.authentication"), new Reference("service.user")]);

$container->register(
    "App\Controllers\AuthenticationPageController",
    App\Controllers\AuthenticationPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig"), new Reference("service.authentication")]);

$container->register(
    "App\Controllers\HomePageController",
    App\Controllers\HomePageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig")]);


$container->register(
    "App\Controllers\TechTalksPageController",
    App\Controllers\TechTalksPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig")]);

$container->register(
    "App\Controllers\InternshipPageController",
    App\Controllers\InternshipPageController::class
)
    ->setArguments([new Reference("service.authorization"), new Reference("twig"), new Reference("repository.internship")]);

return $container;