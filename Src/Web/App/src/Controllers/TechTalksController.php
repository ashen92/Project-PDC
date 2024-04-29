<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateSessionDTO;
use App\Security\AuthorizationService;
use App\Services\TechtalksService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


#[Route('/techtalks')]
class TechTalksController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly TechtalksService $techtalksService
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route([''])]
    public function home(): Response
    {
        return $this->render('techtalks/home.html', [
            'section' => 'home',
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $this->techtalksService->deleteSession($id);
        return new Response(null, 204);
    }

    #[Route('/techtalks/{id}/modify', methods: ['GET'])]
    public function updateGET(int $id): Response
    {
        return $this->render(
            'techtalks/modify.html',
            [
                'section' => 'techtalks',
                'sessions' => $this->techtalksService->getSessionById($id)
            ]
        );
    }

    #[Route('/techtalks/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getSessionByID(Request $request, int $id): Response
    {
        $res = $this->techtalksService->getSessionById($id);
        return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);

    }


    #[Route('/create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render(
            'techtalks/create.html',
            [
                'section' => 'create',
                'groups' => $this->techtalksService->getUserGroups()
            ]
        );
    }

    #[Route('/techtalklist', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render(
            'techtalks/techtalklist.html',
            [
                'section' => 'list',
                'sessions' => $this->techtalksService->getSessionlist()
            ]
        );
    }



    #[Route('/edit/{sessionId}', methods: ['GET'])]
    public function edit(int $sessionId): Response
    {

        $session = $this->techtalksService->getSessionById($sessionId);

        return $this->render(
            'events/edit.html',
            [
                'section' => 'home',
                'session' => $session
            ]
        );
    }

    #[Route('/create', methods: ['POST'])]
    public function createPOST(Request $request): Response
    {
        $data = $request->request->all();
        $Title = $data['sessionTitle'] ?? '';
        $startTimeString = $data['startTime'] ?? '';   //$startTime = DateTimeImmutable::createFromFormat('H:i', $data['startTime']);  
        $endTimeString = $data['endTime'] ?? '';    //$endTime = DateTimeImmutable::createFromFormat('H:i', $data['endTime']);
        $sessionLocation = $data['sessionLocation'] ?? '';
        $description = $data['description'] ?? '';
        $participants = $data['participants'] ?? '';

        $startTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $startTimeString);
        if (!$startTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid start time format');
        }

        $endTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $endTimeString);
        if (!$endTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid end time format');
        }

        if ($this->techtalksService->sessionExists($startTime, $sessionLocation)) {
            $error_message = "A session with the same date and location already exists.";
            return $this->render('techtalks/create.html', ['section' => 'create', 'error_message' => $error_message]);
        }

        $session = new CreateSessionDTO($Title, $startTime, $endTime, $sessionLocation, $description, [$participants]);
        $this->techtalksService->createSession($session);

        return $this->render(
            'techtalks/create.html',
            [
                'section' => 'create'
            ]
        );
    }

    #[Route('/add-participant/{sessionId}', methods: ['POST'])]
    public function addParticipant(int $sessionId, Request $request): Response
    {
        $data = $request->request->all();
        $userGroupId = (int) ($data['userGroupId'] ?? 0);

        if ($userGroupId <= 0) {
            throw new \InvalidArgumentException('Invalid user group ID');
        }

        $this->techtalksService->addParticipantToSession($sessionId, $userGroupId);

        // Redirect back to event details page or any appropriate route
        return $this->redirect('/techtalks/edit/' . $sessionId);
    }

    #[Route('/all', methods: ['GET'])]
    public function all(Request $request): Response
    {
        $queryParams = $request->query->all();

        $startTime = $queryParams['start'];
        $endTime = $queryParams['end'];

        $startTime = new DateTimeImmutable($startTime);
        $endTime = new DateTimeImmutable($endTime);

        $res = $this->techtalksService->getSessions($startTime, $endTime);
        return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/delete/{sessionId}', methods: ['POST'])]
    public function deletePOST(Request $request): Response
    {
        $sessionId = (int) $request->get('sessionId');
        $session = $this->techtalksService->getSessionById($sessionId);
        $this->techtalksService->deleteSession($session);
        return $this->redirect('/techtalks');
    }

}