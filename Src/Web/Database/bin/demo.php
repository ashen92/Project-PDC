<?php
declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

require_once __DIR__ . "/register-types.php";

// Remove database if exists and create a new one with the same name

$params = require __DIR__ . "/doctrine-config.php";
$params["dbname"] = null;

$config = Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/../Entities"),
    isDevMode: true,
);

$conn = Doctrine\DBAL\DriverManager::getConnection($params, $config);

$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'pdc'";
$stmt = $conn->prepare($sql);
$resultSet = $stmt->executeQuery();
$result = $resultSet->fetchFirstColumn();

if ($result) {
    $conn->exec("DROP DATABASE pdc");
    echo "Database dropped successfully\n";

} else {
    echo "Database does not exists\n";
}

$conn->exec("CREATE DATABASE pdc");
echo "Database created successfully\n";

// ------------------------------------------------------------

$params["dbname"] = "pdc";
$conn = Doctrine\DBAL\DriverManager::getConnection($params, $config);
$entityManager = new Doctrine\ORM\EntityManager($conn, $config);
$schemaTool = new Doctrine\ORM\Tools\SchemaTool($entityManager);
$classes = $entityManager->getMetadataFactory()->getAllMetadata();

try {
    $schemaTool->createSchema($classes);
    echo "Schema created successfully\n";
} catch (\Exception $e) {
    echo "An error occurred while creating the schema:\n";
    echo $e->getMessage() . "\n";
}

try {
    echo "Seeding database...\n";
    require_once __DIR__ . "/../demo/SeedDatabase.php";
} catch (\Exception $e) {
    echo "An error occurred while seeding the database:\n";
    echo $e->getMessage() . "\n";
}