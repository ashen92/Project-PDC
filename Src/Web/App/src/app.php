<?php
declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use App\Routing\CustomAnnotationClassLoader;
use Symfony\Component\Routing\RouteCollection;

$controllerDir = __DIR__ . "/Controllers/PageControllers";

$fileLocator = new FileLocator();

$annotationLoader = new CustomAnnotationClassLoader();

$annotationDirectoryLoader = new AnnotationDirectoryLoader($fileLocator, $annotationLoader);

$routes = new RouteCollection();
$routes->addCollection($annotationDirectoryLoader->load($controllerDir));

return $routes;