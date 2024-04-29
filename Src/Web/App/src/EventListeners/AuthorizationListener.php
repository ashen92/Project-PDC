<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Security\Attributes\RequiredAtLeastOne;
use App\Security\Attributes\RequiredPolicy;
use App\Security\Attributes\RequiredRole;
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

        if (!empty($controllerAttributes)) {
            $controllerRequiredRole = $controllerAttributes[0]->newInstance()->role;
            $hasControllerAccess = false;

            if (is_array($controllerRequiredRole)) {
                foreach ($controllerRequiredRole as $role) {
                    if ($this->authzService->hasRole($role)) {
                        $hasControllerAccess = true;
                        break;
                    }
                }
            } else {
                $hasControllerAccess = $this->authzService->hasRole($controllerRequiredRole);
            }

            if (!$hasControllerAccess) {
                throw new AccessDeniedHttpException();
            }
        }

        $requiredRoleAttributes = $reflector->getAttributes(RequiredRole::class);

        if (!empty($requiredRoleAttributes)) {
            $requiredRole = $requiredRoleAttributes[0]->newInstance()->role;
            $hasRole = false;

            if (is_array($requiredRole)) {
                foreach ($requiredRole as $role) {
                    if ($this->authzService->hasRole($role)) {
                        $hasRole = true;
                        break;
                    }
                }
            } else {
                $hasRole = $this->authzService->hasRole($requiredRole);
            }

            if (!$hasRole) {
                throw new AccessDeniedHttpException();
            }
        }

        $requiredPolicyAttributes = $reflector->getAttributes(RequiredPolicy::class);

        if (!empty($requiredPolicyAttributes)) {
            $policyName = $requiredPolicyAttributes[0]->newInstance()->policyName;

            if (!$this->authzService->authorize($policyName)) {
                throw new AccessDeniedHttpException();
            }
        }

        $requiredAtLeastOneAttributes = $reflector->getAttributes(RequiredAtLeastOne::class);

        if (!empty($requiredAtLeastOneAttributes)) {
            $roles = $requiredAtLeastOneAttributes[0]->newInstance()->roles;
            $policies = $requiredAtLeastOneAttributes[0]->newInstance()->policies;

            foreach ($roles as $role) {
                if ($this->authzService->hasRole($role)) {
                    return;
                }
            }

            foreach ($policies as $policy) {
                if ($this->authzService->authorize($policy)) {
                    return;
                }
            }

            throw new AccessDeniedHttpException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ["onKernelController", 0]
        ];
    }
}