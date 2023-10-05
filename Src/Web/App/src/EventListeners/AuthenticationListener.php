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
        $session = $request->getSession();

        $currentRoute = $request->getPathInfo();
        $currentMethod = $request->getMethod();

        $specialRoutes = [
            "/" => ["GET"],
            "/login" => ["GET", "POST"],
            "/signup" => ["GET"],
            "/signup/details" => ["POST"],
            "/signup/submit" => ["POST"],
            "/register" => ["GET"],
        ];
        if (array_key_exists($currentRoute, $specialRoutes)) {
            if (in_array($currentMethod, $specialRoutes[$currentRoute])) {
                if (!$session || !$session->get("is_authenticated")) {
                    return;
                } else {
                    $response = new RedirectResponse("/home");
                    $event->setResponse($response);
                }
            }
        } else {
            if (!$session || !$session->get("is_authenticated")) {
                $response = new RedirectResponse("/");
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest"
        ];
    }
}