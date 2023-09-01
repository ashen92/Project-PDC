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
$container->register(
    "twig.loader",
    Twig\Loader\FilesystemLoader::class
)
    ->setArguments(["../src/Pages"]);

$container->register(
    "twig",
    Twig\Environment::class
)
    ->setArguments([new Reference("twig.loader")]);

$container->register(
    "database.connection",
    App\Services\MySQLConnection::class
)
    ->setArguments(["localhost", "pdc", "root", "root"]);

$container->register(
    "authentication.service",
    App\Services\AuthenticationService::class
)
    ->setArguments([new Reference("user.repository"), new Reference("request.stack")]);

$container->register(
    "authorization.service",
    App\Services\AuthorizationService::class
)
    ->setArguments([new Reference("user.service")]);

$container->register(
    "user.service",
    App\Services\UserService::class
)
    ->setArguments([new Reference("request.stack"), new Reference("user.repository")]);

// Repositories
$container->register("user.repository", App\Repositories\UserRepository::class)
    ->setArguments([new Reference("database.connection")]);

// Controllers
$container->register(
    "App\Controllers\AuthenticationController",
    App\Controllers\AuthenticationController::class
)
    ->setArguments([new Reference("authentication.service")]);

$container->register(
    "App\Controllers\SigninController",
    App\Controllers\SigninController::class
)
    ->setArguments([new Reference("authorization.service"), new Reference("twig")]);


$container->register(
    "App\Controllers\SignupController",
    App\Controllers\SignupController::class
)
    ->setArguments([new Reference("authorization.service"), new Reference("twig")]);


$container->register(
    "App\Controllers\HomeController",
    App\Controllers\HomeController::class
)
    ->setArguments([new Reference("authorization.service"), new Reference("twig")]);


$container->register(
    "App\Controllers\TechTalksController",
    App\Controllers\TechTalksController::class
)
    ->setArguments([new Reference("authorization.service"), new Reference("twig")]);

$container->register(
    "App\Controllers\InternshipController",
    App\Controllers\InternshipController::class
)
    ->setArguments([new Reference("authorization.service"), new Reference("twig")]);

return $container;