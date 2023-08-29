<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

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
    "authentication.database",
    App\Services\DBAuthenticationService::class
)
    ->setArguments([new Reference("database.connection")]);

// DBContext
$container->register("repository.user", App\Models\UserRepository::class)
    ->setArguments([new Reference("database.connection")]);

// Controllers
$container->register(
    "App\Controllers\AuthenticationController",
    App\Controllers\AuthenticationController::class
)
    ->setArguments([new Reference("authentication.database")]);

$container->register(
    "App\Controllers\SigninController",
    App\Controllers\SigninController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\SignupController",
    App\Controllers\SignupController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\HomeController",
    App\Controllers\HomeController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\TechTalksController",
    App\Controllers\TechTalksController::class
)
    ->setArguments([new Reference("twig")]);

return $container;