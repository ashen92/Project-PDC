<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateEventDTO;
use App\Security\AuthorizationService;
use App\Services\EventService;
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
            //'events' => $this->eventService->getEvent()
        ]);
    }

    #[Route('/create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render(
            'events/create.html',
            [
                'section' => 'create',
                'groups' => $this->eventService->getUserGroups()
            ]
        );
    }

    #[Route('/eventlist', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render(
            'events/eventlist.html',
            ['section' => 'list']
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

    #[Route('/create', methods: ['POST'])]
    public function createPOST(Request $request): Response
    {
        $data = $request->request->all();
        $Title = $data['eventTitle'] ?? '';
        $startTimeString = $data['startTime'] ?? '';   //$startTime = DateTimeImmutable::createFromFormat('H:i', $data['startTime']);  
        $endTimeString = $data['endTime'] ?? '';    //$endTime = DateTimeImmutable::createFromFormat('H:i', $data['endTime']);
        $eventLocation = $data['eventLocation'] ?? '';
        $description = $data['description'] ?? '';
        $participants = $data['participants'] ?? '';

        $startTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $startTimeString);
        if (!$startTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid start time format');
        }

        $endTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $endTimeString);
        if (!$endTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid end time format');
        }

        $event = new CreateEventDTO($Title, $startTime, $endTime, $eventLocation, $description, [$participants]);
        $this->eventService->createEvent($event);
        
        return $this->render(
            'events/create.html',
            [
                'section' => 'create'
            ]
        );
    }

    #[Route('/add-participant/{eventId}', methods: ['POST'])]
    public function addParticipant(int $eventId, Request $request): Response
    {
        $data = $request->request->all();
        $userGroupId = (int) ($data['userGroupId'] ?? 0);

        if ($userGroupId <= 0) {
            throw new \InvalidArgumentException('Invalid user group ID');
        }

        $this->eventService->addParticipantToEvent($eventId, $userGroupId);

        // Redirect back to event details page or any appropriate route
        return $this->redirect('/events/edit/' . $eventId);
    }


    #[Route('/delete/{eventId}', methods: ['POST'])]
    public function deletePOST(Request $request): Response
    {
        $eventId = (int) $request->get('eventId');
        $event = $this->eventService->getEventById($eventId);
        $this->eventService->deleteEvent($event);
        return $this->redirect('/events');
    }

    #[Route('/all', methods: ['GET'])]
    public function all(Request $request): Response
    {
        $queryParams = $request->query->all();

        $startTime = $queryParams['start'];
        $endTime = $queryParams['end'];

        $startTime = new DateTimeImmutable($startTime);
        $endTime = new DateTimeImmutable($endTime);

        $res = $this->eventService->getEvents($startTime, $endTime);
        return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);
    }

    // #[Route('/{eventId}', methods:['GET'])]
    // public function getAllEvents()
    // {
    //     $events = $this->eventService->getAllEvents();
    //     header('Content-Type: application/json');
    //     echo json_encode($events);
    // } 
}