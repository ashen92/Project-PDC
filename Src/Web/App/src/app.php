<?php
declare(strict_types=1);

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

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
        methods: "GET"
    )
);

$routes->add(
    "page.signin.get",
    new Routing\Route(
        path: "/",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\AuthenticationPageController::signin",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.signup.get",
    new Routing\Route(
        path: "/signup",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\AuthenticationPageController::signup",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.register.get",
    new Routing\Route(
        path: "/register",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\AuthenticationPageController::register",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.home.get",
    new Routing\Route(
        path: "/home",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\HomePageController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.techtalks.get",
    new Routing\Route(
        path: "/techtalks",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\TechTalksPageController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.internship.get",
    new Routing\Route(
        path: "/internship",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\InternshipPageController::index",
        ],
        methods: "GET"
    )
);

$routes->add(
    "page.internship.view.get",
    new Routing\Route(
        path: "/internship/view",
        defaults: [
            "_controller" => "App\Controllers\PageControllers\InternshipPageController::viewInternships",
        ],
        methods: "GET"
    )
);

return $routes;