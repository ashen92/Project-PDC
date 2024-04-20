<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateEventDTO;
use App\Security\AuthorizationService;
use App\Services\EventService;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/events')]
class EventsController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly EventService $eventService
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route([''])]
    public function home(): Response
    {
        return $this->render('events/home.html', [
            'section' => 'home',
            'events' => $this->eventService->getEvents()
        ]);
    }

    #[Route('/create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render(
            'events/create.html',
            ['section' => 'create']
        );
    }

    #[Route('/edit/{eventId}', methods: ['GET'])]
    public function edit(int $eventId): Response
    {

        $event = $this->eventService->getEventById($eventId);

        return $this->render(
            'events/edit.html',
            [
                'section' => 'home',
                'event' => $event
            ]
        );
    }
    //#[Route('/edit/{eventId}', methods: ['POST'])]
    //public function editPOST(Request $request): Response
    

    #[Route('/create', methods: ['POST'])]
    public function createPOST(Request $request): Response
    {
        $data = $request->request->all();
        $Title = $data['eventTitle'];
        $eventDate = DateTimeImmutable::createFromFormat('Y-m-d', $data['eventDate']);
        $startTime = DateTimeImmutable::createFromFormat('H:i', $data['startTime']);
        $endTime = DateTimeImmutable::createFromFormat('H:i', $data['endTime']);
        $eventLocation = $data['eventLocation'];
        $description = $data['description'];
        $event = new CreateEventDTO($Title, $eventDate , $startTime, $endTime, $eventLocation ,$description);
        $this->eventService->createEvent($event);
        return $this->render(
            'events/create.html',
            ['section' => 'create']
        );
    }

    #[Route('/delete/{eventId}', methods: ['POST'])]
    public function deletePOST(Request $request): Response
    {
        $eventId = (int) $request->get('eventId');
        $event = $this->eventService->getEventById($eventId);
        $this->eventService->deleteEvent($event);
        return $this->redirect('/events');
    }
}