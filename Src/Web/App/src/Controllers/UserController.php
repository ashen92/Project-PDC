<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RequiredRole("ROLE_ADMIN")]
#[Route("/users", name: "users_")]
class UserController extends PageControllerBase
{
	#[Route("", name: "home")]
	public function home(): Response
	{
		return $this->render("users/home.html");
	}

	#[Route("/partners", name: "partners")]
	public function partners(): Response
	{
		return $this->render("users/companylist.html");
	}
}