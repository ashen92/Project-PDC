<?php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

declare(strict_types=1);

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/Entities"),
    isDevMode: true,
);
$connection = DriverManager::getConnection(require_once 'doctrine-config.php', $config);
$entityManager = new EntityManager($connection, $config);