<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class AuthorizationListener implements EventSubscriberInterface
{
    public function __construct(
        private AuthorizationService $authzService,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $reflector = $event->getControllerReflector();

        $controllerReflection = $reflector->getDeclaringClass();
        $controllerAttributes = $controllerReflection->getAttributes(RequiredRole::class);
        $controllerRequiredRole = "";

        $hasControllerAccess = false;

        if (!empty($controllerAttributes)) {
            $controllerRequiredRole = $controllerAttributes[0]->newInstance()->role;

            if (is_array($controllerRequiredRole)) {
                foreach ($controllerRequiredRole as $role) {
                    if ($this->authzService->hasRole($role)) {
                        $hasControllerAccess = true;
                    }
                }
            } else {
                if ($this->authzService->hasRole($controllerRequiredRole)) {
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
                if ($this->authzService->hasRole($role)) {
                    return;
                }
            }
        } else {
            if ($this->authzService->hasRole($requiredRole)) {
                return;
            }
        }

        throw new AccessDeniedHttpException();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ["onKernelController", 0]
        ];
    }
}