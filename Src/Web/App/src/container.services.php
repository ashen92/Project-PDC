<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Reference;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use App\EventListeners\AuthorizationListener;

$dbParams = require_once "doctrine-config.php";

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/Entities"),
    isDevMode: true,
);

$connection = DriverManager::getConnection($dbParams, $config);

$container->register("doctrine.entity_manager", EntityManager::class)
    ->setArguments([$connection, $config]);

$container->register(
    "twig.loader",
    Twig\Loader\FilesystemLoader::class
)
    ->setArguments([__DIR__ . "/Pages"]);

$container->register(
    "twig",
    Twig\Environment::class
)
    ->setArguments([new Reference("twig.loader")]);

$container->register(
    "service.authentication",
    App\Services\AuthenticationService::class
)
    ->setArguments([new Reference("request.stack"), new Reference("doctrine.entity_manager")]);

$container->register(
    "service.user",
    App\Services\UserService::class
)
    ->setArguments([new Reference("doctrine.entity_manager"), new Reference("app.cache")]);

$container->register(
    "listener.authorization",
    AuthorizationListener::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("service.authentication"),
        new Reference("app.cache")
    ]);