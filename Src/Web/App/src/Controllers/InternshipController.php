<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\IPageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class InternshipController implements IPageController
{
    private $templateVariables = array(
        "sectionDisplayName" => "Internship Program",
        "sectionURL" => "internship"
    );

    public function __construct(private Environment $twig)
    {

    }

    public function index(Request $request): Response
    {
        return new Response($this->render("internship/home.html"));
    }

    public function viewInternships(Request $request): Response
    {
        return new Response($this->render("internship/internships.html"));
    }

    public function render(string $template): string
    {
        extract($this->templateVariables);
        return $this->twig->render($template, get_defined_vars());
    }
}