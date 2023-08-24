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
    "page.signup.get",
    new Routing\Route(
        path: "/signup",
        defaults: [
            "_controller" => "App\Controllers\SignupController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.home.get",
    new Routing\Route(
        path: "/home",
        defaults: [
            "_controller" => "App\Controllers\HomeController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "login",
    new Routing\Route(
        path: "/login",
        defaults: [
            "_controller" => "App\Controllers\AuthenticationController::login",
        ],
        methods: "POST"
    )
);

$routes->add(
    "logout",
    new Routing\Route(
        path: "/logout",
        defaults: [
            "_controller" => "App\Controllers\AuthenticationController::logout",
        ],
        methods: "POST"
    )
);

return $routes;