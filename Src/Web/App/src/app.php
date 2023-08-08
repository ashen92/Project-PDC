<?php
declare(strict_types=1);

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('auth', new Routing\Route('/login', [
    '_controller' => 'App\Controller\LeapYearController::index',
]));
$routes->add('home', new Routing\Route('/', [
    '_controller' => 'App\Controller\SigninController::index',
]));

return $routes;