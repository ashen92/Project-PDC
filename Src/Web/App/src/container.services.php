<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Reference;

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
    "service.authentication",
    App\Services\AuthenticationService::class
)
    ->setArguments([new Reference("repository.user"), new Reference("request.stack")]);

$container->register(
    "service.authorization",
    App\Services\AuthorizationService::class
)
    ->setArguments([new Reference("service.user")]);

$container->register(
    "service.user",
    App\Services\UserService::class
)
    ->setArguments([new Reference("request.stack"), new Reference("repository.user")]);

