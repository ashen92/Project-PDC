<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Event;
use App\Interfaces\IEventService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route("/events", name: "events_")]
class EventsController extends PageControllerBase
{
    private IEventService $eventService;

    public function __construct(
        Environment $twig,
        IEventService $eventService
    ) {
        $this->eventService = $eventService;
        parent::__construct($twig);
    }

    #[Route(["", "/"], name: "home")]
    public function home(): Response
    {
        return $this->render("events/home.html", [
            "section" => "home",
            "events" => $this->eventService->getEvents()
        ]);
    }

    #[Route("/create", name: "create_get", methods: ["GET"])]
    public function create(Request $request): Response
    {
        return $this->render(
            "events/create.html",
            ["section" => "create"]
        );
    }

    #[Route("/edit/{eventId}", name: "edit", methods: ["GET"])]
    public function edit(int $eventId): Response
    {

        $event = $this->eventService->getEventById($eventId);

        return $this->render(
            "events/edit.html",
            [
                "section" => "home",
                "event" => $event
            ]
        );
    }
    #[Route("/edit/{eventId}", name: "editPOST", methods: ["POST"])]
    public function editPOST(Request $request): Response
    {
        $eventId = (int) $request->get("eventId") ?? "1";
        $data = $request->request->all();
        $event = $this->eventService->getEventById($eventId);
        $Title = $data["eventTitle"];
        $eventDate = DateTime::createFromFormat("Y-m-d", $data["eventDate"]);
        $startTime = DateTime::createFromFormat("H:i:s", $data["startTime"]);
        $endTime = DateTime::createFromFormat("H:i:s", $data["endTime"]);
        $eventLocation = $data["eventLocation"];
        $description = $data["description"];
        $event->setEventDate($eventDate);
        $event->setTitle($Title);
        $event->setStartTime($startTime);
        $event->setEndTime($endTime);
        $event->setEventLocation($eventLocation);
        $event->setDescription($description);
        $this->eventService->editEvent($event);
        return $this->redirect("/events");
    }

    #[Route("/create", name: "POSTcreate", methods: ["POST"])]
    public function createPOST(Request $request): Response
    {
        $data = $request->request->all();
        $Title = $data["eventTitle"];
        $eventDate = DateTime::createFromFormat("Y-m-d", $data["eventDate"]);
        $startTime = DateTime::createFromFormat("H:i", $data["startTime"]);
        $endTime = DateTime::createFromFormat("H:i", $data["endTime"]);
        $eventLocation = $data["eventLocation"];
        $description = $data["description"];
        $event = new Event($Title, $description, $startTime, $endTime, $eventDate, $eventLocation);
        $this->eventService->createEvent($event);
        return $this->render(
            "events/create.html",
            ["section" => "create"]
        );
    }

    #[Route("/delete/{eventId}", name: "deletePOST", methods: ["POST"])]
    public function deletePOST(Request $request): Response
    {
        $eventId = (int) $request->get("eventId");
        $event = $this->eventService->getEventById($eventId);
        $this->eventService->deleteEvent($event);
        return $this->redirect("/events");
    }

}