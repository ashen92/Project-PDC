<?php
declare(strict_types=1);

namespace App\Controllers\PageControllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function index(Request $request): Response
    {
        return $this->render("techtalks.html");
    }
}