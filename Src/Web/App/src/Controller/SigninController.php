<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SigninController
{
    public function index(Request $request): Response
    {
        return new Response('Hello from home');
    }
}