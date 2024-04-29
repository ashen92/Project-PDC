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

    #[Route('/company', methods: ['GET'])]

    public function company(): Response
    {
        return $this->render('shared/company-profile.html');
    }

    #[Route('/edit', methods: ['GET'])]

    public function edit(): Response
    {
        return $this->render('shared/edit-profile.html');
    }

}

