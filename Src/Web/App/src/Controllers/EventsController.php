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
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $this->eventService->deleteEvent($id);
        return new Response(null, 204);
    }

    #[Route('/events/{id}/modify', methods: ['GET'])]
    public function updateGET(int $id): Response
    {
        return $this->render(
            'events/modify.html',
            [
                'section' => 'events',
                'events' => $this->eventService->getEventById($id)
            ]
        );
    }

    #[Route('/events/{id}/modify', methods: ['POST'])]
    /* public function updatePOST(Request $request): Response|RedirectResponse
    {
        $id = (int) $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        //$isPublished = (bool) $request->get('is_published');

        
        if ($this->eventService->updateEvents($id, $title, $description)) {
            return $this->redirect('/internship-program/internships');
        }

        // TODO: Set errors

        return $this->render(
            'events/modify.html',
            [
                'section' => 'events',
                'event' => $this->eventService->getEventById($id)
            ]
        );
    } */

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getEventByID(Request $request, int $id): Response
    {
        $res = $this->eventService->getEventById($id);
        return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);
    
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
    public function list(Request $request): Response
    {
        /* $queryParams = $request->query->all();

        $startTime = $queryParams['start'];
        $endTime = $queryParams['end'];

        $startTime = new DateTimeImmutable($startTime);
        $endTime = new DateTimeImmutable($endTime); */

        return $this->render(
            'events/eventlist.html',
            ['section' => 'list','events'=>$this->eventService->getEventlist()]
            
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