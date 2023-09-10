<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Interfaces\IAuthenticationService;
use App\Interfaces\IUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class AuthorizationListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private IUserService $userService,
        private IAuthenticationService $authn
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->authn->isAuthenticated()) {
            $userId = (int) $event->getRequest()->getSession()->get("user_id");
            $roles = $this->userService->getUserRoles($userId);
            $this->twig->addGlobal("roles", $roles);
            // logic for permissions
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
        ];
    }
}