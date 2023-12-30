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
	#[Route("")]
	public function home(): Response
	{
		return $this->render(
			"portal/home.html",
			["section" => "home"]
		);
	}

	#[Route("/users")]
	public function users(): Response
	{
		return $this->render(
			"portal/users/home.html",
			["section" => "users"]
		);
	}

	#[Route("/users/create", methods: ["GET"])]
	public function createUser(): Response
	{
		return $this->render(
			"portal/users/create.html",
			["section" => "users"]
		);
	}


	#[Route("/partners")]
	public function partners(): Response
	{
		return $this->render("portal/companylist.html");
	}
}