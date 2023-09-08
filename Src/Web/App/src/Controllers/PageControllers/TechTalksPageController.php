<?php
declare(strict_types=1);

namespace App\Controllers\PageControllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/techtalks", name: "techtalks_")]
class TechTalksPageController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "TechTalks";
    }

    protected function getSectionURL(): string
    {
        return "/techtalks";
    }

    #[Route("", name: "home")]
    public function home(): Response
    {
        return $this->render("techtalks.html");
    }
}