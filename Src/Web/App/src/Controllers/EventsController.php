<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IEventService;
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

    protected function getSectionName(): string
    {
        return "Events";
    }

    protected function getSectionURL(): string
    {
        return "/events";
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

    #[Route("/create", name: "create_post", methods: ["POST"])]
    public function createPost(Request $request): Response
    {
        $this->eventService->addEvent(
            $request->request->get("title"),
            $request->request->get("description")
        );
        return $this->redirect("/events");
    }
}