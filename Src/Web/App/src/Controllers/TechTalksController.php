<?php
declare(strict_types=1);

namespace App\Controllers;


use App\DTOs\CreateSessionDTO;
use App\DTOs\CreateSessionTitleDTO;
use App\Security\AuthorizationService;
use App\Services\TechtalksService;
use DateTimeImmutable;
use InvalidArgumentException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/booksession', methods: ['GET'])]
    public function booksession(): Response
    {
        return $this->render('techtalks/booksession.html', [
            'section' => 'home',
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $this->techtalksService->deleteSession($id);
        return new Response(null, 204);
    }

    /* #[Route('/{id}/deletecompanydata', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deletecompanydata(int $id): Response
    {
        $this->techtalksService->deleteCompanyData($id);
        return new Response(null, 204);
    } */

    #[Route('/{id}/modify', methods: ['GET'])]
    public function updateGET(int $id): Response
    {
        return $this->render(
            'techtalks/modify.html',
            [
                'section' => 'techtalks',
                'session' => $this->techtalksService->getSessionById($id),
                'groups' => $this->techtalksService->getUserGroups()    
            ]
        );
    }

    #[Route('/{id}/modify', methods: ['POST'])]
    public function updatePOST(Request $request): Response|RedirectResponse
    {
        $id = (int) $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $sessionLocation = $request->get('sessionLocation');
        $starttime = $request->get('startTime');
        $endtime = $request->get('endTime');
        $participants = $request->get('participants');
        
        $startTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $starttime);
        if (!$startTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid start time format');
        }

        $endTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $endtime);
        if (!$endTime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Invalid end time format');
        }

        
        if ($this->techtalksService->updateSession($id, $title, $description, $sessionLocation, $startTime, $endTime, [$participants])) {
            return $this->redirect('/techtalks');
        }

        return $this->render(
            'events/modify.html',
            [
                'section' => 'events',
                'event' => $this->techtalksService->getSessionById($id)
            ]
        );
    }

    #[Route('/techtalks/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getSessionByID(Request $request, int $id): Response
    {
        $res = $this->techtalksService->getSessionById($id);
        return new Response(json_encode($res), 200, ['Content-Type' => 'application/json']);

    }


    #[Route('/schedulesession', methods: ['GET'])]
    public function scheduleSession(): Response
    {
        return $this->render(
            'techtalks/schedulesession.html',
            [
                'section' => 'create',
                'groups' => $this->techtalksService->getUserGroups()
            ]
        );
    }

    #[Route('/{id}/scheduletitle', methods: ['GET'])]
    public function scheduletitle(int $id): Response
    {
        return $this->render(
            'techtalks/scheduletitle.html',
            [
                'section' => 'create',
                'session' => $this->techtalksService->getSessionById($id),
            ]
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getList(int $id): Response
    {
        $sessions = $this->techtalksService->getSessionById($id);
        
        return new Response(json_encode($sessions), 200, ['Content-Type' => 'application/json']);

    }

    #[Route('/techtalklist', methods: ['GET'])]
    public function list(): Response
    {
        $sessions = $this->techtalksService->getSessionlist();
        
        return $this->render(
            'techtalks/techtalklist.html',
            [
                'section' => 'list',
                'sessions' => $sessions
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

    #[Route('/{id}/scheduletitle', methods: ['POST'])]
    public function createPOST2(Request $request): Response
    {
        $data = $request->request->all();
        $companyname = $data['companyname'] ?? '';
        $Title = $data['sessionTitle'] ?? '';
        
        $description = $data['description'] ?? '';
       
        $id= (int) $request->get('id');

        $session = new CreateSessionTitleDTO($companyname,$Title,$description);
        $this->techtalksService->createSessionTitle($session, $id);

        return $this->render(
            'techtalks/home.html',
            [
                'section' => 'create'
            ]
            
        );
    }

    #[Route('/createsession', methods: ['POST'])]
    public function createPOST(Request $request): Response
    {

        $data = $request->request->all();
        $techtalksessionnumber = $data['techtalkSessionNumber'] ?? '';
        $startTimeString = $data['startTime'] ?? '';   //$startTime = DateTimeImmutable::createFromFormat('H:i', $data['startTime']);  
        $endTimeString = $data['endTime'] ?? '';    //$endTime = DateTimeImmutable::createFromFormat('H:i', $data['endTime']);
        $sessionLocation = $data['sessionLocation'] ?? '';
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

        $session = new CreateSessionDTO($techtalksessionnumber, $startTime, $endTime, $sessionLocation, [$participants]);
        $this->techtalksService->createSession($session);

        return $this->render(
            'techtalks/schedulesession.html',
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