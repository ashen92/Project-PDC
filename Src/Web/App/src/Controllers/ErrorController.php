<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends PageControllerBase
{
    #[Route('/{any}', name: 'not_found', requirements: ['any' => '.*'], methods: ['GET'], priority: -255)]
    public function notFound(): Response
    {
        return $this->render("404.html", responseStatus: 404);
    }
}