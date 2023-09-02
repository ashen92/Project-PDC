<?php
declare(strict_types=1);

namespace App\Controllers\PageControllers;

use App\Interfaces\IAuthorizationService;
use App\Repositories\InternshipRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class InternshipPageController extends PageControllerBase
{
    public function __construct(
        IAuthorizationService $authz,
        Environment $twig,
        private InternshipRepository $internshipRepository
    ) {
        parent::__construct($authz, $twig);
    }

    protected function getSectionName(): string
    {
        return "Internship Program";
    }

    protected function getSectionURL(): string
    {
        return "/internship";
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