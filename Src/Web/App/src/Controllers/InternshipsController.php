<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/internships", name: "internships_")]
class InternshipsController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "/internships";
    }

    #[Route(["", "/"], name: "home")]
    public function home(Request $request): Response
    {
        return $this->render("internships/home.html");
    }

    #[Route("/cycle", name: "cycle")]
    public function cycle(Request $request): Response
    {
        return $this->render("internships/cycle.html");
    }

    #[Route("/show/all", name: "show_all")]
    public function show_all(): Response
    {
        return $this->render("internships/show_all.html");
    }

    #[Route("/show/{id}", name: "show_one")]
    public function show_one(int $id): Response
    {
        return $this->render("internships/show_one.html", ["id" => $id]);
    }
}