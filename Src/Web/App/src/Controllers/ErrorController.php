<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends ControllerBase
{
    #[Route('/{any}', requirements: ['any' => '.*'], methods: ['GET'], priority: -255)]
    public function notFound(): Response
    {
        return $this->render('404.html', responseStatus: 404);
    }

    public function exception(FlattenException $exception): Response
    {
        $msg = 'Something went wrong! (' . $exception->getMessage() . ')';

        if ($exception->getClass() == 'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException') {
            return $this->render('404.html', responseStatus: 404);
        }

        return new Response($msg, $exception->getStatusCode());
    }
}