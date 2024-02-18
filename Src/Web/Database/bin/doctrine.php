<?php
declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../src/container.php";

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

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/Entities"),
    isDevMode: true,
);
$dbParams = require_once 'doctrine-config.php';
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'requirement_type');
$platform->registerDoctrineTypeMapping('enum', 'requirement_repeat_interval');

return ConsoleRunner::run(
    ConsoleRunner::createHelperSet($entityManager)
);