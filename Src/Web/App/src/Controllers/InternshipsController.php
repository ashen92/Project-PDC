<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredPolicy;
use App\Attributes\RequiredRole;
use App\DTOs\createInternshipDTO;
use App\Models\InternshipCycle;
use App\Security\Identity;
use App\Security\Role;
use App\Services\InternshipService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    Role::InternshipProgram_Admin,
    Role::InternshipProgram_Partner_Admin,
    Role::InternshipProgram_Student,
])]
#[Route('/internship-program/internships')]
class InternshipsController extends PageControllerBase
{
    const int MAX_INTERNSHIP_RESULTS_PER_PAGE = 25;

    public function __construct(
        Environment $twig,
        private readonly InternshipService $internshipService,
    ) {
        parent::__construct($twig);
    }

    #[Route([''])]
    public function internships(Request $request, Identity $identity, ?InternshipCycle $cycle): Response
    {
        if ($cycle === null) {
            return $this->render('internship-program/internships.html', ['section' => 'internships']);
        }

        $queryParams = $request->query->all();

        $searchQuery = $queryParams['q'] ?? null;
        $pageNumber = $queryParams['p'] ?? 1;

        // TODO: Validate query params

        $cycleId = $cycle->getId();
        $orgs = null;

        if ($identity->hasRole(Role::InternshipProgram_Partner)) {
            $userId = $request->getSession()->get('user_id');
            $internships = $this->internshipService
                ->searchInternships(
                    $cycleId,
                    $searchQuery,
                    $userId,
                    self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                    (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                );

            $numberOfResults = $this->internshipService->getInternshipCount(
                $cycleId,
                $searchQuery,
                $userId
            );
        } else {
            $internships = $this->internshipService
                ->searchInternships(
                    $cycleId,
                    $searchQuery,
                    null,
                    self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                    (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                );

            $orgs = $this->internshipService->searchInternshipsGetOrganizations(
                $cycleId,
                $searchQuery,
            );

            $numberOfResults = $this->internshipService->getInternshipCount(
                $cycleId,
                $searchQuery,
                null
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

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $this->internshipService->deleteInternship($id);
        return new Response(null, 204);
    }

    #[Route('/{id}/modify', methods: ['GET'])]
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

    #[Route('/{id}/modify', methods: ['POST'])]
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

    #[RequiredPolicy(InternshipCycle\State::JobCollection)]
    #[Route('/create', methods: ['GET'])]
    public function createGET(Identity $identity): Response
    {
        return $this->render(
            'internship-program/internship/create.html',
            [
                'section' => 'internships',
                'organizations' => $identity->hasRole(Role::InternshipProgram_Admin) ? $this->internshipService->getOrganizations() : null,
            ]
        );
    }

    #[RequiredPolicy(InternshipCycle\State::JobCollection)]
    #[Route('/create', methods: ['POST'])]
    public function createPOST(Request $request, Identity $identity, InternshipCycle $cycle): RedirectResponse
    {
        $orgId = $identity->hasRole(Role::InternshipProgram_Admin) ?
            (int) $request->get('organization') : null;

        $applyOnExternalWebsite = (bool) $request->get('apply-method');
        $externalWebsite = $applyOnExternalWebsite ? $request->get('external-website') : null;

        $dto = new createInternshipDTO(
            $request->get('title'),
            $request->get('description'),
            (int) $request->getSession()->get('user_id'),
            $orgId,
            $applyOnExternalWebsite,
            $externalWebsite,
        );

        // TODO: Validate data

        $this->internshipService->createInternship($cycle->getId(), $dto);
        return $this->redirect('/internship-program/internships');
    }
}