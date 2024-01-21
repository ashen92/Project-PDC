<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredRole;
use App\Controllers\ErrorController;
use App\Services\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class AuthorizationListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private UserService $userService,
        private ErrorController $errorController,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->getPathInfo();
        $currentMethod = $request->getMethod();

        $specialRoutes = [
            "/login" => ["GET", "POST"],
            "/signup" => ["GET", "POST"],
            "/signup/continue" => ["GET", "POST"],
            "/register" => ["GET"],
        ];

        if ($event->getRequest()->getSession()->has("is_authenticated")) {
            $userId = (int) $event->getRequest()->getSession()->get("user_id");
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
            $response = new RedirectResponse("/login?redirect=$currentRoute");
            $event->setResponse($response);
            return;
        }
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $userId = (int) $event->getRequest()->getSession()->get("user_id");

        $reflector = $event->getControllerReflector();

        $controllerReflection = $reflector->getDeclaringClass();
        $controllerAttributes = $controllerReflection->getAttributes(RequiredRole::class);
        $controllerRequiredRole = "";

        $hasControllerAccess = false;

        if (!empty($controllerAttributes)) {
            $controllerRequiredRole = $controllerAttributes[0]->newInstance()->role;

            if (is_array($controllerRequiredRole)) {
                foreach ($controllerRequiredRole as $role) {
                    if ($this->userService->hasRole($userId, $role)) {
                        $hasControllerAccess = true;
                    }
                }
            } else {
                if ($this->userService->hasRole($userId, $controllerRequiredRole)) {
                    $hasControllerAccess = true;
                }
            }

            if (!$hasControllerAccess) {
                $event->setController(fn() => $this->errorController->notFound());
                return;
            }
        }

        $attributes = $reflector->getAttributes(RequiredRole::class);

        if (empty($attributes))
            return;

        $requiredRole = $attributes[0]->newInstance()->role;

        if (is_array($requiredRole)) {
            foreach ($requiredRole as $role) {
                if ($this->userService->hasRole($userId, $role)) {
                    return;
                }
            }
        } else {
            if ($this->userService->hasRole($userId, $requiredRole)) {
                return;
            }
        }

        $event->setController(fn() => $this->errorController->notFound());
        $event->stopPropagation();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
            KernelEvents::CONTROLLER => ["onKernelController", 0]
        ];
    }
}