<?php
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpFoundation\Session\Session;
use App\EventListeners\AuthenticationListener;

require_once __DIR__.'/../vendor/autoload.php';

$routes = include __DIR__.'/../src/app.php';
$container = include __DIR__.'/../src/container.php';

$request = Request::createFromGlobals();
$request->setSession(new Session());

$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
$dispatcher->addSubscriber(new AuthenticationListener());

$controllerResolver = new ContainerControllerResolver($container);
$argumentResolver = new ArgumentResolver();

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);