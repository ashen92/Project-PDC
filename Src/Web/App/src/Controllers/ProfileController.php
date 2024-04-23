<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Security\AuthorizationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/profile')]
class ProfileController extends ControllerBase
{
    #[Route([''])]
    public function profile(): Response
    {
        return $this->render('shared/profile.html');
    }
}

