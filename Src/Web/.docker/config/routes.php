<?php
declare(strict_types=1);

use App\Routing\AttributeClassLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$controllerDir = __DIR__ . "/Controllers";
$fileLocator = new FileLocator();
$routeAttributeLoader = new AttributeClassLoader();
$attributeDirectoryLoader = new AttributeDirectoryLoader($fileLocator, $routeAttributeLoader);
$routes->addCollection($attributeDirectoryLoader->load($controllerDir));

return $routes;