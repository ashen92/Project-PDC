<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateUserDTO;
use App\Exceptions\UserExistsException;
use App\Security\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole('Admin')]
#[Route('/portal')]
class PortalController extends ControllerBase
{
	public function __construct(
		Environment $twig,
		AuthorizationService $authzService,
		private readonly UserService $userService
	) {
		parent::__construct($twig, $authzService);
	}

	#[Route('')]
	public function home(): Response
	{
		return $this->render(
			'portal/home.html',
			[
				'section' => 'home',
				'active_users' => intval('135'),
				'stu_count' => intval('98'),
				'app_partners' => intval('30'),
				'pen_partners' => intval('27'),
				'co_count' => intval('5'),
			]
		);
	}

	#[Route('/home/link1')]
	public function homeLink1(): Response
	{
		return $this->render('portal/home/link1.html');
	}

	#[Route('/home/link2')]
	public function homeLink2(): Response
	{
		return $this->render('portal/home/link2.html');
	}

	#[Route('/home/link3')]
	public function homeLink3(): Response
	{
		return $this->render('portal/home/link3.html');
	}

	#[Route('/users')]
	public function users(): Response
	{
		return $this->render(
			'portal/users/home.html',
			[
				'section' => 'users',
				'users' => $this->userService->searchUsers(null, null),
			]
		);
	}

	#[Route('/users/create', methods: ['GET'])]
	public function createUser(): Response
	{
		return $this->render(
			'portal/users/create.html',
			['section' => 'users']
		);
	}

	#[Route('/users/create', methods: ['POST'])]
	public function createUserPost(Request $request): Response|RedirectResponse
	{
		$dto = new CreateUserDTO(
			$request->request->get('user-type'),
			$request->request->get('email'),
			$request->request->get('first-name'),
			$request->request->get('student-email'),
			$request->request->get('send-email'),
			$request->request->get('full-name'),
			$request->request->get('registration-number'),
			$request->request->get('index-number'),
			$request->request->get('organization'),
		);

		// TODO: validate DTO

		try {
			$this->userService->createUser($dto);
		} catch (UserExistsException) {

			// TODO: Set error message

			return $this->render(
				'portal/users/create.html',
				['section' => 'users']
			);
		}

		return $this->redirect('/portal/users/create');
	}

	#[Route('/groups', methods: ['GET'])]
	public function groups(): Response
	{
		return $this->render(
			'portal/groups/home.html',
			[
				'section' => 'groups',
				'groups' => $this->userService->searchGroups(null, null),
			]
		);
	}
}