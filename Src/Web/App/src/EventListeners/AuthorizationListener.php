<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class AuthorizationListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private AuthorizationService $authzService,
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
            $roles = $this->authzService->getUserRolesAsStrings($userId);
            $this->twig->addGlobal("user_roles", $roles);
            // logic for permissions

            if (array_key_exists($currentRoute, $specialRoutes)) {
                $response = new RedirectResponse("/home");
                $event->setResponse($response);
                return;
            }
        } else {
            if (array_key_exists($currentRoute, $specialRoutes)) {
                if (in_array($currentMethod, $specialRoutes[$currentRoute])) {
                    return;
                }
            }
            $response = new RedirectResponse("/login?redirect=$currentRoute");
            $event->setResponse($response);
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
                    if ($this->authzService->hasRole($userId, $role)) {
                        $hasControllerAccess = true;
                    }
                }
            } else {
                if ($this->authzService->hasRole($userId, $controllerRequiredRole)) {
                    $hasControllerAccess = true;
                }
            }

            if (!$hasControllerAccess) {
                throw new AccessDeniedHttpException();
            }
        }

        $attributes = $reflector->getAttributes(RequiredRole::class);

        if (empty($attributes))
            return;

        $requiredRole = $attributes[0]->newInstance()->role;

        if (is_array($requiredRole)) {
            foreach ($requiredRole as $role) {
                if ($this->authzService->hasRole($userId, $role)) {
                    return;
                }
            }
        } else {
            if ($this->authzService->hasRole($userId, $requiredRole)) {
                return;
            }
        }

        throw new AccessDeniedHttpException();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
            KernelEvents::CONTROLLER => ["onKernelController", 0]
        ];
    }
}