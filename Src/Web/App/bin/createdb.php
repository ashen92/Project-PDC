<?php
declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

$params = require __DIR__ . "/../src/doctrine-config.php";
$params["dbname"] = null;

$config = Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__ . "/Entities"),
    isDevMode: true,
);

$conn = Doctrine\DBAL\DriverManager::getConnection($params, $config);

$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'pdc'";
$stmt = $conn->prepare($sql);
$resultSet = $stmt->executeQuery();
$result = $resultSet->fetchFirstColumn();

if (!$result) {
    $conn->exec("CREATE DATABASE pdc");
    echo "Database created successfully\n";
} else {
    echo "Database already exists\n";
}