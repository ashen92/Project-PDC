<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/internships", name: "internships_")]
class InternshipController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "/internships";
    }

    #[Route("", name: "home")]
    public function home(Request $request): Response
    {
        return $this->render("internships/home.html");
    }

    #[Route("/show/all", name: "show_all")]
    public function show(): Response
    {
        return $this->render("internships/internships.html");
    }
}