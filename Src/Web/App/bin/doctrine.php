<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../src/container.php";

$entityManager = $container->get("doctrine.entity_manager");

return ConsoleRunner::run(
    ConsoleRunner::createHelperSet($entityManager)
);