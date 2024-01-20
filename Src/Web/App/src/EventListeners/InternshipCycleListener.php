<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Controllers\ErrorController;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InternshipCycleListener implements EventSubscriberInterface
{
    public function __construct(
        private IUserService $userService,
        private IInternshipCycleService $internshipCycleService,
        private ErrorController $errorController,
    ) {

    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getControllerReflector()->getDeclaringClass()->name;
        if (
            $controller != "App\Controllers\InternshipProgramController" &&
            $controller != "App\Controllers\InternshipController" &&
            $controller != "App\Controllers\RequirementController"
        )
            return;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ["onKernelController", 100],
        ];
    }
}