<?php
declare(strict_types=1);

namespace App\Routing;

use Symfony\Component\Routing\Route;

class AttributeClassLoader extends \Symfony\Component\Routing\Loader\AttributeClassLoader
{
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $annot)
    {
        if ("__invoke" === $method->getName()) {
            $route->setDefault("_controller", $class->getName());
        } else {
            $route->setDefault("_controller", $class->getName() . "::" . $method->getName());
        }
    }
}