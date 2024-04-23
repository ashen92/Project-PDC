<?php
declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpFoundation\RequestStack;

$routes = require_once __DIR__ . "/../src/routes.php";
$container = require_once __DIR__ . "/../src/container.php";

$requestStack = new RequestStack();

$request = Request::createFromGlobals();
$request->setSession($container->get("session"));

$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
$dispatcher->addSubscriber($container->get("listener.authentication"));
$dispatcher->addSubscriber($container->get("listener.authorization"));
$dispatcher->addSubscriber($container->get("listener.exception"));

$controllerResolver = new ContainerControllerResolver($container);
$argumentResolver = new ArgumentResolver(
    null,
    [
        new RequestAttributeValueResolver(),
        new RequestValueResolver(),
        new SessionValueResolver(),
        $container->get('internship_cycle_resolver'),
        new DefaultValueResolver(),
        new VariadicValueResolver(),
    ]
);

$kernel = new HttpKernel($dispatcher, $controllerResolver, $requestStack, $argumentResolver);

try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo $e;
}