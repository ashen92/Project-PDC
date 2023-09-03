<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Reference;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

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