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
	protected function getSectionName(): string
	{
		return "User Management";
	}

	protected function getSectionURL(): string
	{
		return "/users";
	}

	#[Route("", name: "home")]
	public function home(): Response
	{
		return $this->render("users/home.html");
	}
}