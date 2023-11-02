<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/events", name: "events_")]
class EventsController extends PageControllerBase
{
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
        return $this->render("events/home.html");
    }

    #[Route("/create", name: "create", methods: ["GET"])]
    public function create(Request $request): Response
    {
        return $this->render("events/create.html");
    }
}