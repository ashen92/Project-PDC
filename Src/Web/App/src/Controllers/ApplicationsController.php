<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/internship-program/applications")]
class ApplicationsController extends PageControllerBase
{
    #[Route([""])]
    public function applications(): Response
    {
        return $this->render(
            "internship-program/applications/home.html",
            ["section" => "applications"]
        );
    }
}