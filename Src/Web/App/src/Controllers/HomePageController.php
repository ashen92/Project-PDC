<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends PageControllerBase
{
    protected function getSectionName(): string {
        return "Home";
    }
    
    protected function getSectionURL(): string {
        return "/home";
    }

    public function index(Request $request): Response
    {
        return $this->render("home.html");
    }
}