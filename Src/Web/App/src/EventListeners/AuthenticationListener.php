<?php
declare(strict_types=1);

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationListener implements EventSubscriberInterface
{
    private const SPECIAL_ROUTES = [
        "/login" => ["GET", "POST"],
        "/signup" => ["GET", "POST"],
        "/signup/continue" => ["GET", "POST"],
        "/register" => ["GET"],
    ];

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->getPathInfo();
        $currentMethod = $request->getMethod();

        if ($event->getRequest()->getSession()->has("is_authenticated")) {
            if (array_key_exists($currentRoute, $this::SPECIAL_ROUTES)) {
                $response = new RedirectResponse("/home");
                $event->setResponse($response);
                return;
            }
        } else {
            if (array_key_exists($currentRoute, $this::SPECIAL_ROUTES)) {
                if (in_array($currentMethod, $this::SPECIAL_ROUTES[$currentRoute])) {
                    return;
                }
            }
            $response = new RedirectResponse("/login?redirect=$currentRoute");
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
        ];
    }
}