<?php
declare(strict_types=1);

use App\Security\IdentityProvider;
use App\Security\IdentityResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

\Doctrine\DBAL\Types\Type::addType(
    'requirement_type',
    'App\DoctrineTypes\Requirement\TypeType'
);
\Doctrine\DBAL\Types\Type::addType(
    'requirement_repeat_interval',
    'App\DoctrineTypes\Requirement\RepeatIntervalType'
);
\Doctrine\DBAL\Types\Type::addType(
    'requirement_fulfill_method',
    'App\DoctrineTypes\Requirement\FulFillMethodType'
);
\Doctrine\DBAL\Types\Type::addType(
    'user_requirement_status',
    'App\DoctrineTypes\UserRequirement\StatusType'
);
\Doctrine\DBAL\Types\Type::addType(
    'application_status',
    'App\DoctrineTypes\Application\StatusType'
);
\Doctrine\DBAL\Types\Type::addType(
    'internship_status',
    'App\DoctrineTypes\Internship\StatusType'
);

$cachedContainerFile = __DIR__ . '/../cache/container.php';

if (getenv('IS_PRODUCTION') && file_exists($cachedContainerFile)) {
    require_once $cachedContainerFile;
    return new CachedContainer();
}

$container = new ContainerBuilder();

$container->register(
    'session',
    Symfony\Component\HttpFoundation\Session\Session::class
)
    ->setPublic(true);

$container->register(
    'security.identity_provider',
    IdentityProvider::class
)
    ->setArguments([new Reference('pdo_mysql_connection')])
    ->setPublic(true);

$container->register(
    'security.identity_resolver',
    IdentityResolver::class
)
    ->setArguments([new Reference('security.identity_provider')])
    ->setPublic(true);

$container->register(
    'internship_cycle_resolver',
    App\ValueResolver\InternshipCycleResolver::class
)
    ->setArguments([new Reference('repository.internship_program')])
    ->setPublic(true);

#region Database Connection --------------------------------------------------------

$container->setParameter('pdo_mysql.host', 'localhost');
$container->setParameter('pdo_mysql.dbname', 'pdc');
$container->setParameter('pdo_mysql.user', 'root');
$container->setParameter('pdo_mysql.password', 'root');

$container->register(
    'pdo_mysql_connection',
    PDO::class
)
    ->setArguments([
        'mysql:host=%pdo_mysql.host%;dbname=%pdo_mysql.dbname%',
        '%pdo_mysql.user%',
        '%pdo_mysql.password%'
    ]);

$container->setParameter('doctrine.params', require_once 'doctrine-config.php');

$container->register(
    'doctrine.config',
    Doctrine\ORM\ORMSetup::class
)
    ->setFactory([Doctrine\ORM\ORMSetup::class, 'createAttributeMetadataConfiguration'])
    ->setArguments([
        array(__DIR__ . '/Entities'),
        true,
    ]);

$container->register(
    'doctrine.connection',
    Doctrine\DBAL\DriverManager::class
)
    ->setFactory([Doctrine\DBAL\DriverManager::class, 'getConnection'])
    ->setArguments([
        '%doctrine.params%',
        new Reference('doctrine.config')
    ]);

$container->register(
    'doctrine.entity_manager',
    Doctrine\ORM\EntityManager::class
)
    ->setArguments([
        new Reference('doctrine.connection'),
        new Reference('doctrine.config')
    ]);

#endregion

#region Repositories ---------------------------------------------------------------

$container->register(
    'repository.authorization',
    App\Security\AuthorizationRepository::class
)
    ->setArguments([
        new Reference('pdo_mysql_connection'),
    ]);

$container->register(
    'repository.user',
    App\Repositories\UserRepository::class
)
    ->setArguments([
        new Reference('pdo_mysql_connection'),
    ]);

$container->register(
    'repository.internship',
    App\Repositories\InternshipRepository::class
)
    ->setArguments([
        new Reference('pdo_mysql_connection'),
    ]);

$container->register(
    'repository.requirement',
    App\Repositories\RequirementRepository::class
)
    ->setArguments([
        new Reference('pdo_mysql_connection'),
        new Reference('doctrine.entity_manager'),
    ]);

$container->register(
    'repository.internship_program',
    App\Repositories\InternshipProgramRepository::class
)
    ->setArguments([new Reference('pdo_mysql_connection'),]);

$container->register(
    'repository.intern_monitoring',
    App\Repositories\InternMonitoringRepository::class
)
    ->setArguments([new Reference('pdo_mysql_connection'),]);

#endregion

#region Twig -----------------------------------------------------------------------

$container->register(
    'twig.loader',
    Twig\Loader\FilesystemLoader::class
)
    ->setArguments([__DIR__ . '/Pages']);

$container->register(
    'twig.runtime_loader.security',
    App\TwigExtension\SecurityRuntimeLoader::class
)
    ->setArguments([new Reference('twig.runtime_extension.security')]);

$container->register(
    'twig.runtime_extension.security',
    App\TwigExtension\SecurityRuntimeExtension::class
)
    ->setArguments([new Reference('service.authorization')]);

$container->register(
    'twig.extension.security',
    App\TwigExtension\SecurityExtension::class
);

$container->register(
    'twig.extension',
    App\TwigExtension\Extension::class
);

$container->register(
    'twig',
    Twig\Environment::class
)
    ->setArguments([new Reference('twig.loader')])
    ->addMethodCall('addRuntimeLoader', [new Reference('twig.runtime_loader.security')])
    ->addMethodCall('addExtension', [new Reference('twig.extension.security')])
    ->addMethodCall('addExtension', [new Reference('twig.extension')])
    ->addMethodCall('addGlobal', ['app', ['session' => new Reference('session')]])
    ->setPublic(true);

#endregion

#region Services -------------------------------------------------------------------

$container->register(
    'password_hasher',
    App\Services\PasswordHasher::class
);

$container->register(
    'service.authentication',
    App\Services\AuthenticationService::class
)
    ->setArguments([
        new Reference('repository.user'),
        new Reference('password_hasher')
    ]);

$container->register(
    'service.authorization',
    App\Security\AuthorizationService::class
)
    ->setArguments([
        new Reference('repository.authorization'),
        new Reference('session'),
    ]);

$container->register(
    'service.user',
    App\Services\UserService::class
)
    ->setArguments([
        new Reference('repository.user'),
        new Reference('password_hasher'),
        new Reference('service.email'),
    ]);

$container->register(
    'service.internship',
    App\Services\InternshipService::class
)
    ->setArguments([
        new Reference('repository.internship'),
        new Reference('service.internship_program'),
        new Reference('service.file_storage')
    ]);

$container->register(
    'service.internship_program',
    App\Services\InternshipProgramService::class
)
    ->setArguments([
        new Reference('repository.internship_program'),
        new Reference('repository.user'),
        new Reference('service.user'),
    ]);

$container->register(
    'service.requirement',
    App\Services\RequirementService::class
)
    ->setArguments([
        new Reference('repository.requirement'),
        new Reference('service.internship_program'),
        new Reference('service.file_storage')
    ]);

$container->register(
    'service.intern_monitoring',
    App\Services\InternMonitoringService::class
)
    ->setArguments([
        new Reference('repository.intern_monitoring'),
        new Reference('service.file_storage'),
    ]);

$container->register(
    'service.event',
    App\Services\EventService::class
)
    ->setArguments([
        new Reference('doctrine.entity_manager')
    ]);

$container->register(
    'service.email',
    App\Services\EmailService::class
)
    ->setArguments([
        new Reference('http_client'),
        'http://localhost:3000/api/emails'
    ]);

$container->register(
    'service.file_storage',
    App\Services\FileStorageService::class
)
    ->setArguments([
        new Reference('http_client'),
        'http://localhost:5000/api/files'
    ]);

$container->register(
    'http_client',
    Symfony\Component\HttpClient\HttpClient::class
)
    ->setFactory([Symfony\Component\HttpClient\HttpClient::class, 'create']);

#endregion

#region Event Listeners

$container->register(
    'listener.exception',
    Symfony\Component\HttpKernel\EventListener\ErrorListener::class
)
    ->setArguments([
        'App\Controllers\ErrorController::exception',
    ])
    ->setPublic(true);

$container->register(
    'listener.authorization',
    App\EventListeners\AuthorizationListener::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
    ])
    ->setPublic(true);

$container->register(
    'listener.internship_program',
    App\EventListeners\InternshipProgramListener::class
)
    ->setArguments([
        new Reference('repository.internship_program'),
    ])
    ->setPublic(true);

#endregion

#region API Controllers

$container->register(
    'App\Controllers\API\InternshipsAPIController',
    \App\Controllers\API\InternshipsAPIController::class
)
    ->setArguments([
        new Reference('service.internship'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\API\InternMonitoringAPIController',
    \App\Controllers\API\InternMonitoringAPIController::class
)
    ->setArguments([
        new Reference('service.intern_monitoring'),
    ])
    ->setPublic(true);

$container->register(
    'user_management_handler',
    \App\Services\UserManagementHandler::class
)
    ->setArguments([
        new Reference('pdo_mysql_connection'),
    ]);

$container->register(
    'App\Controllers\UserManagementController',
    \App\Controllers\UserManagementController::class
)
    ->setArguments([
        new Reference('user_management_handler'),
    ])
    ->setPublic(true);

#endregion

#region Controllers

$container->register(
    'App\Controllers\ErrorController',
    \App\Controllers\ErrorController::class
)
    ->setArguments([new Reference('twig')])
    ->setPublic(true);

$container->register(
    'App\Controllers\AuthenticationController',
    \App\Controllers\AuthenticationController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authentication'),
        new Reference('service.email')
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\HomeController',
    \App\Controllers\HomeController::class
)
    ->setArguments([new Reference('twig')])
    ->setPublic(true);

$container->register(
    'App\Controllers\TechTalksController',
    \App\Controllers\TechTalksController::class
)
    ->setArguments([new Reference('twig')])
    ->setPublic(true);

$container->register(
    'App\Controllers\InternshipProgramController',
    \App\Controllers\InternshipProgramController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.user'),
        new Reference('service.internship_program'),
        new Reference('service.requirement'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\InternMonitoringController',
    \App\Controllers\InternMonitoringController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.requirement'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\InternshipsController',
    \App\Controllers\InternshipsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.internship'),
        new Reference('service.user')
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\ApplicationsController',
    \App\Controllers\ApplicationsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.internship'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\RequirementsController',
    \App\Controllers\RequirementsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.requirement')
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\PortalController',
    \App\Controllers\PortalController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.user'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\EventsController',
    \App\Controllers\EventsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.event')
    ])
    ->setPublic(true);

#endregion

$container->compile();

$dumper = new Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
file_put_contents(
    $cachedContainerFile,
    $dumper->dump(['class' => 'CachedContainer'])
);

return $container;