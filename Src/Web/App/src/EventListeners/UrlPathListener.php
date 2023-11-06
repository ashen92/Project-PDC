<?php
declare(strict_types=1);

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class UrlPathListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
    ) {

    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->getPathInfo();
        $paths = explode("/", $currentRoute);
        $this->twig->addGlobal("current_section", $paths[1] ?? "");
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => "onKernelRequest",
        ];
    }
}