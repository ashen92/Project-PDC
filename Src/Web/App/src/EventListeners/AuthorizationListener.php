<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredRole;
use App\Controllers\ErrorController;
use App\Interfaces\IAuthenticationService;
use App\Interfaces\IUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class AuthorizationListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private IUserService $userService,
        private CacheInterface $cache,
        private ErrorController $errorController,
        private SessionInterface $session
    ) {
    }

    private function isAuthenticated(): bool
    {
        if ($this->session->has("is_authenticated"))
            return true;
        return false;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->getPathInfo();
        $currentMethod = $request->getMethod();

        $specialRoutes = [
            "/login" => ["GET", "POST"],
            "/signup" => ["GET"],
            "/signup/details" => ["POST"],
            "/signup/submit" => ["POST"],
            "/register" => ["GET"],
        ];

        if ($this->isAuthenticated()) {
            $userId = (int) $this->session->get("user_id");
            $roles = $this->userService->getUserRoles($userId);
            $this->twig->addGlobal("user_roles", $roles);
            // logic for permissions

            if (array_key_exists($currentRoute, $specialRoutes)) {
                $response = new RedirectResponse("/home");
                $event->setResponse($response);
                return;
            }
            return;
        } else {
            if (array_key_exists($currentRoute, $specialRoutes)) {
                if (in_array($currentMethod, $specialRoutes[$currentRoute])) {
                    return;
                }
            }
            $response = new RedirectResponse("/login");
            $event->setResponse($response);
            return;
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        $controller = is_array($controller) ? get_class($controller[0]) : get_class($controller);

        $cacheKey = "roles_for_" . md5($controller);
        $cachedRole = $this->cache->get($cacheKey, function () use ($controller) {
            $controllerReflection = new \ReflectionClass($controller);
            $attributes = $controllerReflection->getAttributes(RequiredRole::class);
            $requiredRole = "";
            if (!empty($attributes)) {
                $requiredRole = $attributes[0]->newInstance()->role;
            }
            return $requiredRole;
        });

        if (!$this->userService->hasRequiredRole((int) $this->session->get("user_id"), $cachedRole)) {
            $event->setController(fn() => $this->errorController->notFound());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
            KernelEvents::CONTROLLER => "onKernelController"
        ];
    }
}