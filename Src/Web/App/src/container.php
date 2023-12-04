<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

$container->register(
    "session",
    Symfony\Component\HttpFoundation\Session\Session::class
);

$container->register(
    "app.cache",
    Symfony\Component\Cache\Adapter\ApcuAdapter::class
)
    ->setArguments(["app"]);

// Services

$dbParams = require_once "doctrine-config.php";

$config = Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/Entities"),
    isDevMode: true,
);

$connection = Doctrine\DBAL\DriverManager::getConnection($dbParams, $config);

$container->register(
    "doctrine.entity_manager",
    Doctrine\ORM\EntityManager::class
)
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

$container->register("password_hasher", App\Services\PasswordHasher::class);

$container->register(
    "service.authentication",
    App\Services\AuthenticationService::class
)
    ->setArguments([
        new Reference("session"),
        new Reference("service.user"),
        new Reference("password_hasher")
    ]);

$container->register(
    "service.user",
    App\Services\UserService::class
)
    ->setArguments([
        new Reference("doctrine.entity_manager"),
        new Reference("app.cache"),
        new Reference("password_hasher")
    ]);

$container->register("service.user_group", App\Services\UserGroupService::class)
    ->setArguments([
        new Reference("doctrine.entity_manager"),
        new Reference("app.cache")
    ]);

$container->register(
    "service.internship",
    App\Services\InternshipService::class
)
    ->setArguments([new Reference("doctrine.entity_manager"), new Reference("app.cache")]);

$container->register(
    "service.internship_cycle",
    App\Services\InternshipCycleService::class
)
    ->setArguments([
        new Reference("doctrine.entity_manager"),
        new Reference("app.cache")
    ]);

$container->register(
    "service.requirement",
    App\Services\RequirementService::class
)
    ->setArguments([
        new Reference("doctrine.entity_manager"),
        new Reference("app.cache"),
        new Reference("service.file_storage")
    ]);

$container->register(
    "service.event",
    App\Services\EventService::class
)
    ->setArguments([new Reference("doctrine.entity_manager"), new Reference("app.cache")]);

$container->register(
    "service.email",
    App\Services\EmailAPI::class
)
    ->setArguments([new Reference("http_client")]);

$container->register(
    "service.file_storage",
    App\Services\FileStorageService::class
)
    ->setArguments([new Reference("http_client")]);

$container->register(
    "http_client",
    Symfony\Component\HttpClient\HttpClient::class
)
    ->setFactory([Symfony\Component\HttpClient\HttpClient::class, "create"]);

$container->register(
    "listener.authorization",
    App\EventListeners\AuthorizationListener::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("app.cache"),
        new Reference("App\Controllers\ErrorController"),
        new Reference("session")
    ]);

// Controllers

$container->register(
    "App\Controllers\ErrorController",
    \App\Controllers\ErrorController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\AuthenticationController",
    \App\Controllers\AuthenticationController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.authentication"),
        new Reference("service.user"),
        new Reference("service.email")
    ]);

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
    "App\Controllers\InternshipProgramController",
    \App\Controllers\InternshipProgramController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("service.user_group"),
        new Reference("service.internship_cycle")
    ]);

$container->register(
    "App\Controllers\InternshipController",
    \App\Controllers\InternshipController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.internship"),
        new Reference("service.user")
    ]);

$container->register(
    "App\Controllers\RequirementController",
    \App\Controllers\RequirementController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("service.requirement")
    ]);

$container->register(
    "App\Controllers\UserController",
    \App\Controllers\UserController::class
)
    ->setArguments([new Reference("twig")]);

$container->register(
    "App\Controllers\EventsController",
    \App\Controllers\EventsController::class
)
    ->setArguments([new Reference("twig"), new Reference("service.event")]);

return $container;