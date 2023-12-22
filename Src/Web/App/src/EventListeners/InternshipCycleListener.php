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

        $session = $event->getRequest()->getSession();

        if ($session->has("active_internship_cycle_id") && $session->has("latest_internship_cycle_id"))
            return;

        $session->set("active_internship_cycle_id", $this->internshipCycleService->getLatestActiveInternshipCycle()?->getId());
        $session->set("latest_internship_cycle_id", $this->internshipCycleService->getLatestInternshipCycle()?->getId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => "onKernelController",
        ];
    }
}