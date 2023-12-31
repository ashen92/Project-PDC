<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Interfaces\IUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RequiredRole("ROLE_ADMIN")]
#[Route("/portal", name: "users_")]
class PortalController extends PageControllerBase
{
	public function __construct(
		\Twig\Environment $twig,
		private IUserService $userService
	) {
		parent::__construct($twig);
	}

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

	#[Route("/users/create", methods: ["POST"])]
	public function createUserPost(Request $request): Response|RedirectResponse
	{
		$dto = new CreateUserDTO(
			$request->request->get("user-type"),
			$request->request->get("email"),
			$request->request->get("first-name"),
			$request->request->get("student-email"),
			$request->request->get("send-email"),
			$request->request->get("full-name"),
			$request->request->get("registration-number"),
			$request->request->get("index-number"),
			$request->request->get("organization"),
		);

		// TODO: validate DTO

		try {
			$this->userService->createUser($dto);
		} catch (UserExistsException $th) {

			// TODO: Set error message

			return $this->render(
				"portal/users/create.html",
				["section" => "users"]
			);
		}

		return $this->redirect("/portal/users/create");
	}

	#[Route("/partners")]
	public function partners(): Response
	{
		return $this->render("portal/companylist.html");
	}
}