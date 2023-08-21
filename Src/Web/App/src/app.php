<?php
declare(strict_types=1);

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add(
    "page.signin.get",
    new Routing\Route(
        path: "/",
        defaults: [
            "_controller" => "App\Controllers\SigninController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.signin.post",
    new Routing\Route(
        path: "/",
        defaults: [
            "_controller" => "App\Controllers\SigninController::login",
        ],
        methods: "POST"
    )
);

return $routes;