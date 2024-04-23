<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/techtalks')]
class TechTalksController extends ControllerBase
{
    #[Route([''])]
    public function home(): Response
    {
        return $this->render('techtalks/home.html');
    }

    #[Route('/create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('techtalks/create.html');
    }
}