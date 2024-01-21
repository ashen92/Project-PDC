<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends PageControllerBase
{
    #[Route([""], name: "home")]
    public function home(): Response
    {
        return $this->render("home.html");
    }
}