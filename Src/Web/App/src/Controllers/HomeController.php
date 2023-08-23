<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController
{
    public function __construct(private Environment $twig)
    {

    }

    public function index(Request $request): Response
    {
        return new Response("Hello from Home");
    }
}