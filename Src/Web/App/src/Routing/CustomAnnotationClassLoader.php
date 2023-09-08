<?php
declare(strict_types=1);

namespace App\Routing;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;

class CustomAnnotationClassLoader extends AnnotationClassLoader
{
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        if ("__invoke" === $method->getName()) {
            $route->setDefault("_controller", $class->getName());
        } else {
            $route->setDefault("_controller", $class->getName() . "::" . $method->getName());
        }
    }
}