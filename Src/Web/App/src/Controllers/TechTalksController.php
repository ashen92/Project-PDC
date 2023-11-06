<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/techtalks", name: "techtalks_")]
class TechTalksController extends PageControllerBase
{
    #[Route(["", "/"], name: "home")]
    public function home(): Response
    {
        return $this->render("techtalks.html");
    }
}