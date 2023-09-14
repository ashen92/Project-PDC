<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredRole;
use App\Interfaces\IAuthenticationService;
use App\Interfaces\IUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
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
        private IAuthenticationService $authn,
        private CacheInterface $cache
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->authn->isAuthenticated()) {
            $userId = (int) $event->getRequest()->getSession()->get("user_id");
            $roles = $this->userService->getUserRoles($userId);
            $this->twig->addGlobal("roles", $roles);
            // logic for permissions
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        $controller = $event->getController();
        $controller = is_array($controller) ? get_class($controller[0]) : get_class($controller);

        $cacheKey = 'roles_for_' . md5($controller);
        $cachedRole = $this->cache->get($cacheKey, function () use ($controller) {
            $controllerReflection = new \ReflectionClass($controller);
            $attributes = $controllerReflection->getAttributes(RequiredRole::class);
            $requiredRole = "";
            if (!empty($attributes)) {
                $attribute = $attributes[0]->newInstance();
                $requiredRole = $attribute->role;
            }
            return $requiredRole;
        });

        if (!$this->userService->hasRequiredRole((int) $session->get("user_id"), $cachedRole)) {
            $event->setController(fn() => new Response("Page Not Found", 404));
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