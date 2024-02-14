<?php
declare(strict_types=1);

namespace App\EventListeners;

use App\Attributes\RequiredPolicy;
use App\Models\InternshipCycle\State;
use App\Repositories\InternshipProgramRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class InternshipProgramListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly InternshipProgramRepository $internshipProgramRepository,
    ) {

    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getControllerReflector()->getDeclaringClass()->name;
        if (
            $controller != "App\Controllers\InternshipProgramController" &&
            $controller != "App\Controllers\InternshipsController" &&
            $controller != "App\Controllers\RequirementController"
        )
            return;

        $reflector = $event->getControllerReflector();
        $attributes = $reflector->getAttributes(RequiredPolicy::class);

        if (empty($attributes))
            return;

        $requiredState = $attributes[0]->newInstance()->policy;
        if (!$requiredState instanceof State)
            return;

        $activeCycle = $this->internshipProgramRepository->findLatestActiveCycle();
        $currentState = $activeCycle->getActiveState();

        if ($requiredState !== $currentState) {
            throw new AccessDeniedHttpException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ["onKernelController", 100],
        ];
    }
}