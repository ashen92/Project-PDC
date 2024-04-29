<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
    'internship_cycle_resolver',
    App\ValueResolver\InternshipCycleResolver::class
)
    ->setArguments([new Reference('repository.internship_program')])
    ->setPublic(true);

#region Database Connection --------------------------------------------------------

$container->setParameter('pdo_mysql.host', getenv('DB_HOST'));
$container->setParameter('pdo_mysql.dbname', getenv('DB_DATABASE'));
$container->setParameter('pdo_mysql.user', getenv('DB_USERNAME'));
$container->setParameter('pdo_mysql.password', getenv('DB_PASSWORD'));

$container->register(
    'pdo_mysql_connection',
    PDO::class
)
    ->setArguments([
        'mysql:host=%pdo_mysql.host%;dbname=%pdo_mysql.dbname%',
        '%pdo_mysql.user%',
        '%pdo_mysql.password%'
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

$container->register(
    'repository.application',
    App\Repositories\ApplicationRepository::class
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
    \App\Security\TwigExtension\SecurityRuntimeLoader::class
)
    ->setArguments([new Reference('twig.runtime_extension.security')]);

$container->register(
    'twig.runtime_extension.security',
    \App\Security\TwigExtension\SecurityRuntimeExtension::class
)
    ->setArguments([new Reference('service.authorization')]);

$container->register(
    'twig.extension.security',
    \App\Security\TwigExtension\SecurityExtension::class
);

$container->register(
    'twig',
    Twig\Environment::class
)
    ->setArguments([new Reference('twig.loader')])
    ->addMethodCall('addRuntimeLoader', [new Reference('twig.runtime_loader.security')])
    ->addMethodCall('addExtension', [new Reference('twig.extension.security')])
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

// Policies

$container->register(
    'security.policy.employed',
    App\Security\Policies\EmploymentStatusPolicy::class
)
    ->setArguments(['employed']);

$container->register(
    'security.policy.unemployed',
    App\Security\Policies\EmploymentStatusPolicy::class
)
    ->setArguments(['unemployed']);

$container->register(
    'security.policy.job_hunt_round_1',
    App\Security\Policies\JobHuntRoundPolicy::class
)
    ->setArguments([1]);

$container->register(
    'security.policy.job_hunt_round_2',
    App\Security\Policies\JobHuntRoundPolicy::class
)
    ->setArguments([2]);

// Policy handlers

$container->register(
    'security.policy_handler.employment_status',
    App\Security\PolicyHandlers\EmploymentStatusPolicyHandler::class
)
    ->setArguments([
        new Reference('repository.intern_monitoring'),
    ]);

$container->register(
    'security.policy_handler.job_hunt_round',
    App\Security\PolicyHandlers\JobHuntRoundPolicyHandler::class
);

$container->register(
    'service.authorization',
    App\Security\AuthorizationService::class
)
    ->setArguments([
        new Reference('repository.authorization'),
        new Reference('session'),
    ])
    ->addMethodCall(
        'addPolicyHandler',
        [
            'Employed',
            new Reference('security.policy.employed'),
            new Reference('security.policy_handler.employment_status')
        ]
    )
    ->addMethodCall(
        'addPolicyHandler',
        [
            'Unemployed',
            new Reference('security.policy.unemployed'),
            new Reference('security.policy_handler.employment_status')
        ]
    )
    ->addMethodCall(
        'addPolicyHandler',
        [
            'JobHuntRound1',
            new Reference('security.policy.job_hunt_round_1'),
            new Reference('security.policy_handler.job_hunt_round')
        ]
    )
    ->addMethodCall(
        'addPolicyHandler',
        [
            'JobHuntRound2',
            new Reference('security.policy.job_hunt_round_2'),
            new Reference('security.policy_handler.job_hunt_round')
        ]
    );

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
    'service.application',
    App\Services\ApplicationService::class
)
    ->setArguments([
        new Reference('repository.application'),
    ]);

$container->register(
    'service.event',
    App\Services\EventService::class
)
    ->setArguments([
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
    'listener.authentication',
    App\EventListeners\AuthenticationListener::class
)
    ->setPublic(true);

$container->register(
    'listener.authorization',
    App\EventListeners\AuthorizationListener::class
)
    ->setArguments([
        new Reference('service.authorization'),
    ])
    ->setPublic(true);

#endregion

#region API Controllers

$container->register(
    'App\Controllers\API\InternshipProgramAPIController',
    \App\Controllers\API\InternshipProgramAPIController::class
)
    ->setArguments([
        new Reference('service.internship_program'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\API\InternshipsAPIController',
    \App\Controllers\API\InternshipsAPIController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.internship'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\API\ApplicationsAPIController',
    \App\Controllers\API\ApplicationsAPIController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.application'),
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
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\AuthenticationController',
    \App\Controllers\AuthenticationController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.authentication'),
        new Reference('service.email')
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\HomeController',
    \App\Controllers\HomeController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\TechTalksController',
    \App\Controllers\TechTalksController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\InternshipProgramController',
    \App\Controllers\InternshipProgramController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
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
        new Reference('service.authorization'),
        new Reference('service.intern_monitoring'),
        new Reference('service.requirement'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\InternshipsController',
    \App\Controllers\InternshipsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
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
        new Reference('service.authorization'),
        new Reference('service.internship'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\RequirementsController',
    \App\Controllers\RequirementsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.requirement')
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\PortalController',
    \App\Controllers\PortalController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.user'),
    ])
    ->setPublic(true);

$container->register(
    'App\Controllers\EventsController',
    \App\Controllers\EventsController::class
)
    ->setArguments([
        new Reference('twig'),
        new Reference('service.authorization'),
        new Reference('service.event')
    ])
    ->setPublic(true);

$container->register(
        'App\Controllers\ProfileController',
        \App\Controllers\ProfileController::class
    )
        ->setArguments([new Reference('twig')])
        ->setPublic(true);

#endregion

$container->compile();

$dumper = new Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
file_put_contents(
    $cachedContainerFile,
    $dumper->dump(['class' => 'CachedContainer'])
);

return $container;