<?php
declare(strict_types=1);

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationListener implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $currentRoute = $request->getPathInfo();
        $currentMethod = $request->getMethod();

        $excludedRoutes = [
            "/" => ["GET"],
            "/login" => ["GET", "POST"],
            "/signup" => ["GET"]
        ];
        if (array_key_exists($currentRoute, $excludedRoutes)) {
            if (in_array($currentMethod, $excludedRoutes[$currentRoute]))
                return;
        }

        $session = $request->getSession();

        if (!$session || !$session->get("is_authenticated")) {
            $response = new RedirectResponse("/");
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest"
        ];
    }
}