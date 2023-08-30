<?php
declare(strict_types=1);

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface IPageController
{
    public function index(Request $request): Response;
    public function render(string $template): string;
}