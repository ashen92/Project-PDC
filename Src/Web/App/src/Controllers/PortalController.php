<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RequiredRole("ROLE_ADMIN")]
#[Route("/portal", name: "users_")]
class PortalController extends PageControllerBase
{
	#[Route("", name: "home")]
	public function home(): Response
	{
		return $this->render("portal/home.html");
	}

	#[Route("/partners", name: "partners")]
	public function partners(): Response
	{
		return $this->render("portal/companylist.html");
	}
}