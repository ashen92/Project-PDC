<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

\Doctrine\DBAL\Types\Type::addType(
    "requirement_type",
    "App\DoctrineTypes\Requirement\TypeType"
);
\Doctrine\DBAL\Types\Type::addType(
    "requirement_repeat_interval",
    "App\DoctrineTypes\Requirement\RepeatIntervalType"
);
\Doctrine\DBAL\Types\Type::addType(
    "requirement_fulfill_method",
    "App\DoctrineTypes\Requirement\FulFillMethodType"
);
\Doctrine\DBAL\Types\Type::addType(
    "permission_action",
    "App\DoctrineTypes\Permission\ActionType"
);
\Doctrine\DBAL\Types\Type::addType(
    "permission_resource",
    "App\DoctrineTypes\Permission\ResourceType"
);

$cachedContainerFile = __DIR__ . "/../cache/container.php";

if (getenv("IS_PRODUCTION") && file_exists($cachedContainerFile)) {
    require_once $cachedContainerFile;
    return new CachedContainer();
}

$container = new ContainerBuilder();

$container->register(
    "session",
    Symfony\Component\HttpFoundation\Session\Session::class
)
    ->setPublic(true);

// Database Connection --------------------------------------------------------

$container->setParameter("pdo_mysql.host", "localhost");
$container->setParameter("pdo_mysql.dbname", "pdc");
$container->setParameter("pdo_mysql.user", "root");
$container->setParameter("pdo_mysql.password", "root");

$container->register(
    "pdo_mysql_connection",
    PDO::class
)
    ->setArguments([
        "mysql:host=%pdo_mysql.host%;dbname=%pdo_mysql.dbname%",
        "%pdo_mysql.user%",
        "%pdo_mysql.password%"
    ]);

// Repositories ---------------------------------------------------------------

$container->register(
    "repository.user",
    App\Repositories\UserRepository::class
)
    ->setArguments([
        new Reference("pdo_mysql_connection"),
        new Reference("doctrine.entity_manager")
    ]);

$container->register(
    "repository.internship_cycle",
    App\Repositories\InternshipCycleRepository::class
)
    ->setArguments([new Reference("doctrine.entity_manager")]);

$container->register(
    "repository.internship",
    App\Repositories\InternshipRepository::class
)
    ->setArguments([
        new Reference("pdo_mysql_connection"),
        new Reference("doctrine.entity_manager")
    ]);

$container->register(
    "repository.requirement",
    App\Repositories\RequirementRepository::class
)
    ->setArguments([
        new Reference("pdo_mysql_connection"),
        new Reference("doctrine.entity_manager"),
    ]);

$container->register(
    "repository.internship_program",
    App\Repositories\InternshipProgramRepository::class
)
    ->setArguments([new Reference("pdo_mysql_connection"),]);

// Services

$container->setParameter("doctrine.params", require_once "doctrine-config.php");

$container->register(
    "doctrine.config",
    Doctrine\ORM\ORMSetup::class
)
    ->setFactory([Doctrine\ORM\ORMSetup::class, "createAttributeMetadataConfiguration"])
    ->setArguments([
        array(__DIR__ . "/Entities"),
        true,
    ]);

$container->register(
    "doctrine.connection",
    Doctrine\DBAL\DriverManager::class
)
    ->setFactory([Doctrine\DBAL\DriverManager::class, "getConnection"])
    ->setArguments([
        "%doctrine.params%",
        new Reference("doctrine.config")
    ]);

$container->register(
    "doctrine.entity_manager",
    Doctrine\ORM\EntityManager::class
)
    ->setArguments([
        new Reference("doctrine.connection"),
        new Reference("doctrine.config")
    ]);

$container->register(
    "twig.loader",
    Twig\Loader\FilesystemLoader::class
)
    ->setArguments([__DIR__ . "/Pages"]);

$container->register(
    "twig",
    Twig\Environment::class
)
    ->setArguments([new Reference("twig.loader")])->setPublic(true);

$container->register(
    "password_hasher",
    App\Services\PasswordHasher::class
);

$container->register(
    "service.authentication",
    App\Services\AuthenticationService::class
)
    ->setArguments([
        new Reference("service.user"),
        new Reference("password_hasher")
    ]);

$container->register(
    "service.user",
    App\Services\UserService::class
)
    ->setArguments([
        new Reference("repository.user"),
        new Reference("password_hasher"),
        new Reference("service.email"),
    ]);

$container->register(
    "service.internship",
    App\Services\InternshipService::class
)
    ->setArguments([
        new Reference("repository.internship"),
        new Reference("repository.user"),
        new Reference("service.internship_cycle"),
        new Reference("service.file_storage")
    ]);

$container->register(
    "service.internship_cycle",
    App\Services\InternshipCycleService::class
)
    ->setArguments([
        new Reference("repository.internship_cycle"),
        new Reference("repository.internship_program"),
        new Reference("repository.user"),
        new Reference("service.user"),
        new Reference("service.email"),
    ]);

$container->register(
    "service.requirement",
    App\Services\RequirementService::class
)
    ->setArguments([
        new Reference("repository.requirement"),
        new Reference("service.internship_cycle"),
        new Reference("service.file_storage")
    ]);

$container->register(
    "service.event",
    App\Services\EventService::class
)
    ->setArguments([
        new Reference("doctrine.entity_manager")
    ]);

$container->register(
    "service.email",
    App\Services\EmailService::class
)
    ->setArguments([
        new Reference("http_client"),
        "http://localhost:3000/api/emails"
    ]);

$container->register(
    "service.file_storage",
    App\Services\FileStorageService::class
)
    ->setArguments([
        new Reference("http_client"),
        "http://localhost:5000/api/files"
    ]);

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
        new Reference("App\Controllers\ErrorController"),
    ])
    ->setPublic(true);

$container->register(
    "listener.internship_cycle",
    App\EventListeners\InternshipCycleListener::class
)
    ->setArguments([
        new Reference("service.user"),
        new Reference("service.internship_cycle"),
        new Reference("App\Controllers\ErrorController"),
    ])
    ->setPublic(true);

// API Controllers

$container->register(
    "App\Controllers\API\InternshipController",
    \App\Controllers\API\InternshipController::class
)
    ->setArguments([
        new Reference("service.internship"),
        new Reference("service.user"),
    ])
    ->setPublic(true);

// Controllers

$container->register(
    "App\Controllers\ErrorController",
    \App\Controllers\ErrorController::class
)
    ->setArguments([new Reference("twig")])
    ->setPublic(true);

$container->register(
    "App\Controllers\AuthenticationController",
    \App\Controllers\AuthenticationController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.authentication"),
        new Reference("service.user"),
        new Reference("service.email")
    ])
    ->setPublic(true);

$container->register(
    "App\Controllers\HomeController",
    \App\Controllers\HomeController::class
)
    ->setArguments([new Reference("twig")])
    ->setPublic(true);

$container->register(
    "App\Controllers\TechTalksController",
    \App\Controllers\TechTalksController::class
)
    ->setArguments([new Reference("twig")])
    ->setPublic(true);

$container->register(
    "App\Controllers\InternshipProgramController",
    \App\Controllers\InternshipProgramController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("service.internship_cycle"),
        new Reference("service.requirement"),
    ])
    ->setPublic(true);

$container->register(
    "App\Controllers\InternshipController",
    \App\Controllers\InternshipController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.internship"),
        new Reference("service.user")
    ])
    ->setPublic(true);

$container->register(
    "App\Controllers\RequirementController",
    \App\Controllers\RequirementController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
        new Reference("service.requirement")
    ])
    ->setPublic(true);

$container->register(
    "App\Controllers\PortalController",
    \App\Controllers\PortalController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.user"),
    ])
    ->setPublic(true);

$container->register(
    "App\Controllers\EventsController",
    \App\Controllers\EventsController::class
)
    ->setArguments([
        new Reference("twig"),
        new Reference("service.event")
    ])
    ->setPublic(true);

$container->compile();

$dumper = new Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
file_put_contents(
    $cachedContainerFile,
    $dumper->dump(["class" => "CachedContainer"])
);

return $container;