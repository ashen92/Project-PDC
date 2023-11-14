<?php
declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use App\Routing\CustomAnnotationClassLoader;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$controllerDir = __DIR__ . "/Controllers";
$fileLocator = new FileLocator();
$routeAnnotationLoader = new CustomAnnotationClassLoader();
$annotationDirectoryLoader = new AnnotationDirectoryLoader($fileLocator, $routeAnnotationLoader);
$routes->addCollection($annotationDirectoryLoader->load($controllerDir));

return $routes;