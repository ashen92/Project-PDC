<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../src/container.php";

$entityManager = $container->get("doctrine.entity_manager");

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'requirement_type');
$platform->registerDoctrineTypeMapping('enum', 'requirement_repeat_interval');

return ConsoleRunner::run(
    ConsoleRunner::createHelperSet($entityManager)
);