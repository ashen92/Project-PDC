<?php
declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternshipController extends PageControllerBase
{
    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "internship";
    }

    public function index(Request $request): Response
    {
        return $this->render("internship/home.html");
    }

    public function viewInternships(Request $request): Response
    {
        return $this->render("internship/internships.html");
    }

}