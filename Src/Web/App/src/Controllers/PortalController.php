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
		$studentCount = $this->userService->countStudentUsers();
		$activeUsersCount = $this->userService->countActiveUsers();
		$coordinatorsCount = $this->userService->countCoordinators();

		return $this->render(
			'portal/home.html',
			[
				'section' => 'home',
				'activeUsers' => $activeUsersCount,
				'studentCount' => $studentCount,
				'coordinatorsCount' => $coordinatorsCount,
				'app_partners' => '30',
				'pen_partners' => '27',
			]
		);
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

	#[Route('/users/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
	public function delete(int $id): Response
	{
		$this->userService->deleteUser($id);
		return new Response(null, 204);
	}

	#[Route('/users/{id}/activate', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function activate(int $id): Response
	{
		$this->userService->activateUser($id);
		return new Response(null, 204);
	}

	#[Route('/users/{id}/deactivate', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function deactivate(int $id): Response
	{
		$this->userService->deactivateUser($id);
		return new Response(null, 204);
	}

	#[Route('/users/{id}/addusertoGroup', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function add(int $id): Response
	{
		$this->userService->addUser($id);
		return new Response(null, 204);
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

	#[Route('/home/link1')]
	public function findUsers(): Response
	{
		$activeUsers = $this->userService->findActiveUsers();
		$activeUsersCount = $this->userService->countActiveUsers();

		return $this->render(
			'portal/home/link1.html',
			[
				'section' => 'home',
				'users' => $activeUsers,
				'activeUsers' => $activeUsersCount,
			]
		);
	}

	#[Route('/home/link2')]
	public function findStudents(): Response
	{
		$studentUsers = $this->userService->findStudentUsers();
		$studentCount = $this->userService->countStudentUsers();

		return $this->render(
			'portal/home/link2.html',
			[
				'section' => 'home',
				'users' => $studentUsers,
				'studentCount' => $studentCount,
			]
		);
	}

	#[Route('/home/link3')]
	public function findCoods(): Response
	{
		$coordinators = $this->userService->findCoordinators();
		$coordinatorsCount = $this->userService->countCoordinators();

		return $this->render(
			'portal/home/link3.html',
			[
				'section' => 'home',
				'users' => $coordinators,
				'coordinatorsCount' => $coordinatorsCount,
			]
		);
	}

}