<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\createInternshipDTO;
use App\Models\Internship;
use App\Models\InternshipCycle;
use App\Models\InternshipProgram\createApplication;
use App\Security\Attributes\RequiredAtLeastOne;
use App\Security\Attributes\RequiredRole;
use App\Security\AuthorizationService;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    'InternshipProgramAdmin',
    'InternshipProgramPartnerAdmin',
    'InternshipProgramStudent',
])]
#[Route('/internship-program')]
class InternshipsController extends ControllerBase
{
    const int MAX_INTERNSHIP_RESULTS_PER_PAGE = 25;

    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private readonly InternshipService $internshipService,
    ) {
        parent::__construct($twig, $authzService);
    }

    #[RequiredAtLeastOne(['Admin'], ['JobHuntFirstRound'])]
    #[Route(['/internships'])]
    public function internships(Request $request, ?InternshipCycle $cycle): Response
    {
        if ($cycle === null) {
            return $this->render('internship-program/internships.html', ['section' => 'internships']);
        }

        $queryParams = $request->query->all();

        $searchQuery = $queryParams['q'] ?? null;
        $pageNumber = $queryParams['p'] ?? 1;

        $orgIds = $queryParams['c'] ?? null;
        if ($orgIds) {
            $orgIds = explode(',', $orgIds);
            $orgIds = array_map('intval', $orgIds);
        }

        $visibility = $queryParams['v'] ?? null;
        $visibility = $visibility ? Internship\Visibility::tryFrom($visibility) : null;

        $isApproved = $queryParams['a'] ?? null;
        if ($isApproved) {
            if ($isApproved === 'approved') {
                $isApproved = true;
            } elseif ($isApproved === 'pending') {
                $isApproved = false;
            } else {
                $isApproved = null;
            }
        }

        // TODO: Validate query params

        $cycleId = $cycle->getId();
        $orgs = null;

        if ($this->hasRole('InternshipProgramPartner')) {
            $userId = $request->getSession()->get('user_id');
            $internships = $this->internshipService
                ->searchInternships(
                    $cycleId,
                    $searchQuery,
                    null,
                    $visibility,
                    $isApproved,
                    self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                    (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE),
                    $userId
                );

            $numberOfResults = $this->internshipService->countInternships(
                $cycleId,
                $searchQuery,
                null,
                $visibility,
                $isApproved,
                $userId
            );
        } else {
            if ($this->hasRole('InternshipProgramStudent')) {
                $internships = $this->internshipService
                    ->searchInternships(
                        $cycleId,
                        $searchQuery,
                        $orgIds,
                        Internship\Visibility::Public ,
                        true,
                        self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                        (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                    );

                $numberOfResults = $this->internshipService->countInternships(
                    $cycleId,
                    $searchQuery,
                    $orgIds,
                    $visibility,
                    true,
                    null
                );
            } else {
                $internships = $this->internshipService
                    ->searchInternships(
                        $cycleId,
                        $searchQuery,
                        $orgIds,
                        $visibility,
                        $isApproved,
                        self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                        (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                    );

                $numberOfResults = $this->internshipService->countInternships(
                    $cycleId,
                    $searchQuery,
                    $orgIds,
                    $visibility,
                    $isApproved,
                    null
                );
            }

            $orgs = $this->internshipService->getOrganizationsForSearchQuery(
                $cycleId,
                $searchQuery,
                $visibility,
                $isApproved
            );
        }

        $pages = (int) ceil($numberOfResults / self::MAX_INTERNSHIP_RESULTS_PER_PAGE);

        return $this->render(
            'internship-program/internships.html',
            [
                'section' => 'internships',
                'internships' => $internships,
                'organizations' => $orgs,
                'page' => $pageNumber,
                'pages' => $pages,
            ]
        );
    }

    #[Route('/internships/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $this->internshipService->deleteInternship($id);
        return new Response(null, 204);
    }

    #[Route('/internships/{id}/modify', methods: ['GET'])]
    public function updateGET(int $id): Response
    {
        return $this->render(
            'internship-program/internship/modify.html',
            [
                'section' => 'internships',
                'internship' => $this->internshipService->getInternship($id)
            ]
        );
    }

    #[Route('/internships/{id}/modify', methods: ['POST'])]
    public function updatePOST(Request $request): Response|RedirectResponse
    {
        $id = (int) $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $isPublished = (bool) $request->get('is_published');

        // TODO: Validate data

        if ($this->internshipService->updateInternship($id, $title, $description, $isPublished)) {
            return $this->redirect('/internship-program/internships');
        }

        // TODO: Set errors

        return $this->render(
            'internship-program/internship/modify.html',
            [
                'section' => 'internships',
                'internship' => $this->internshipService->getInternship($id)
            ]
        );
    }

    #[Route('/internships/create', methods: ['GET'])]
    public function createGET(): Response
    {
        return $this->render(
            'internship-program/internship/create.html',
            [
                'section' => 'internships',
                'organizations' => $this->hasRole('InternshipProgramAdmin') ? $this->internshipService->getOrganizations() : null,
            ]
        );
    }

    #[Route('/internships/create', methods: ['POST'])]
    public function createPOST(Request $request, InternshipCycle $cycle): RedirectResponse
    {
        $orgId = $this->hasRole('InternshipProgramAdmin') ?
            (int) $request->get('organization') : null;

        $dto = new createInternshipDTO(
            $request->get('title'),
            $request->get('description'),
            (int) $request->getSession()->get('user_id'),
            $orgId,
        );

        // TODO: Validate data

        $this->internshipService->createInternship($cycle->getId(), $dto);
        return $this->redirect('/internship-program/internships');
    }

    #[Route('/internships/{id}/apply', methods: ['POST'])]
    public function applyPOST(Request $request, int $id): RedirectResponse
    {
        $userId = $request->getSession()->get('user_id');

        $files = $request->files->get('application-file');
        if ($files && !is_array($files)) {
            $files = [$files];
        }

        // TODO: Validate data

        if (!$this->internshipService->createApplication(new createApplication($id, $userId, $files))) {
            // TODO: Set errors
        }

        return $this->redirect('/internship-program/internships');
    }

    #[Route('/round-2', methods: ['GET'])]
    public function round2GET(InternshipCycle $cycle): Response
    {
        if ($this->hasRole('InternshipProgramStudent')) {
            return $this->render(
                'internship-program/round-2/home-student.html',
                [
                    'section' => 'round-2',
                    'jobRoles' => $this->internshipService->getJobRoles($cycle->getId()),
                ]
            );
        }

        if ($this->hasRole('InternshipProgramPartner')) {
            return $this->render(
                'internship-program/round-2/home-partner.html',
                [
                    'section' => 'round-2',
                    'jobRoles' => $this->internshipService->getJobRoles($cycle->getId()),
                ]
            );
        }

        return $this->render(
            'internship-program/round-2/home-admin.html',
            [
                'section' => 'round-2',
                'jobRoles' => $this->internshipService->getJobRoles($cycle->getId()),
            ]
        );
    }

    #[Route('/round-2/job-roles/{id}', methods: ['GET'])]
    public function jobRoleStudentsGET(int $id): Response
    {
        return $this->render(
            'internship-program/round-2/job-role/students.html',
            [
                'section' => 'round-2',
                'jobRole' => $this->internshipService->getJobRole($id),
                'students' => $this->internshipService->getStudentsByJobRole($id),
            ]
        );
    }

    #[Route('/round-2/job-roles/add', methods: ['POST'])]
    public function jobRoleAdd(Request $request, InternshipCycle $cycle): RedirectResponse
    {
        $name = $request->get('name');
        // TODO: Validate data

        if (!$this->internshipService->createJobRole($cycle->getId(), $name)) {
            // TODO: Set errors
        }
        return $this->redirect('/internship-program/round-2');
    }

    #[Route('/round-2/job-roles/edit', methods: ['POST'])]
    public function jobRoleEdit(Request $request): RedirectResponse
    {
        $id = (int) $request->get('id');
        $name = $request->get('name');
        // TODO: Validate data

        if (!$this->internshipService->modifyJobRole($id, $name)) {
            // TODO: Set errors
        }
        return $this->redirect('/internship-program/round-2');
    }

    #[Route('/round-2/job-roles/delete', methods: ['POST'])]
    public function jobRoleDelete(Request $request): RedirectResponse
    {
        $id = (int) $request->get('id');
        // TODO: Validate data

        if (!$this->internshipService->deleteJobRole($id)) {
            // TODO: Set errors
        }
        return $this->redirect('/internship-program/round-2');
    }
}