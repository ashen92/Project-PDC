<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "Home";
    }

    protected function getSectionURL(): string
    {
        return "/home";
    }

    #[Route(["/", "/home"], name: "home")]
    public function home(): Response
    {
        return $this->render("home.html");
    }
}