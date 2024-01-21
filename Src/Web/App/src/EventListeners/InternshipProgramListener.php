<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Controllers\ErrorController;
use App\Interfaces\IUserService;
use App\Repositories\InternshipProgramRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InternshipProgramListener implements EventSubscriberInterface
{
    public function __construct(
        private IUserService $userService,
        private InternshipProgramRepository $internshipProgramRepository,
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