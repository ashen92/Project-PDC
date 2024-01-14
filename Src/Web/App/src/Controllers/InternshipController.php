<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\RequiredRole;
use App\Interfaces\IInternshipService;
use App\Interfaces\IUserService;
use App\Security\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[RequiredRole([
    Role::InternshipProgram_Admin,
    Role::InternshipProgram_Partner,
    Role::InternshipProgram_Student,
])]
#[Route("/internship-program/internships")]
class InternshipController extends PageControllerBase
{
    const int MAX_INTERNSHIP_RESULTS_PER_PAGE = 20;

    public function __construct(
        Environment $twig,
        private IInternshipService $internshipService,
        private IUserService $userService,
    ) {
        parent::__construct($twig);
    }

    #[Route(["", "/"])]
    public function internships(Request $request): Response
    {
        $userId = $request->getSession()->get("user_id");
        $latestInternshipCycleId = $request->getSession()->get("latest_internship_cycle_id");

        $queryParams = $request->query->all();

        $searchQuery = $queryParams["q"] ?? null;
        $pageNumber = $queryParams["p"] ?? 1;

        // TODO: Validate query params

        $internships = [];
        $numberOfResults = 0;

        if ($this->userService->hasRole($userId, Role::InternshipProgram_Partner_Admin)) {
            $internships = $this->internshipService
                ->getInternshipsBy(
                    $latestInternshipCycleId,
                    $userId,
                    $searchQuery,
                    self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                    (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                );
            $numberOfResults = $this->internshipService->getNumberOfInternships(
                $latestInternshipCycleId,
                $userId,
                $searchQuery
            );
        } else {
            $internships = $this->internshipService
                ->getInternshipsBy(
                    $latestInternshipCycleId,
                    null,
                    $searchQuery,
                    self::MAX_INTERNSHIP_RESULTS_PER_PAGE,
                    (int) (($pageNumber - 1) * self::MAX_INTERNSHIP_RESULTS_PER_PAGE)
                );

            $numberOfResults = $this->internshipService->getNumberOfInternships(
                $latestInternshipCycleId,
                null,
                $searchQuery
            );
        }

        $pages = (int) ceil($numberOfResults / self::MAX_INTERNSHIP_RESULTS_PER_PAGE);

        $i = array_map(fn($internship) => $internship->internship, $internships);
        $organizations = $this->internshipService->getOrganizationsFrom($i);

        return $this->render(
            "internship-program/internships.html",
            [
                "section" => "internships",
                "internships" => $internships,
                "organizations" => $organizations,
                "page" => $pageNumber,
                "pages" => $pages,
            ]
        );
    }

    #[Route("/{id}", methods: ["DELETE"], requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
    {
        $this->internshipService->deleteInternshipById($id);
        return new Response(null, 204);
    }

    #[Route("/{id}/applicants")]
    public function internshipApplicants(int $id): Response
    {
        return $this->render(
            "internship-program/internship/applicants.html",
            [
                "section" => "internships",
                "internship" => $this->internshipService->getInternship($id),
                "applicants" => $this->internshipService->getApplications($id)
            ]
        );
    }

    #[Route("/{id}/modify", methods: ["GET"])]
    public function updateGET(int $id): Response
    {
        return $this->render(
            "internship-program/internship/modify.html",
            [
                "section" => "internships",
                "internship" => $this->internshipService->getInternship($id)
            ]
        );
    }

    #[Route("/{id}/modify", methods: ["POST"])]
    public function updatePOST(Request $request): Response|RedirectResponse
    {
        $id = (int) $request->get("id");
        $title = $request->get("title");
        $description = $request->get("description");
        $isPublished = (bool) $request->get("is_published");

        // TODO: Validate data

        if ($this->internshipService->updateInternship($id, $title, $description, $isPublished)) {
            return $this->redirect("/internship-program/internships");
        }

        // TODO: Set errors

        return $this->render(
            "internship-program/internship/modify.html",
            [
                "section" => "internships",
                "internship" => $this->internshipService->getInternship($id)
            ]
        );
    }

    #[Route("/create", methods: ["GET"])]
    public function createGET(): Response
    {
        return $this->render("internship-program/internship/create.html", ["section" => "internships"]);
    }

    #[Route("/create", methods: ["POST"])]
    public function createPOST(Request $request): RedirectResponse
    {
        $title = $request->get("title");
        $description = $request->get("description");
        $ownerId = (int) $request->getSession()->get("user_id");
        $organizationId = (int) $request->get("organization_id");
        $isPublished = (bool) $request->get("is_published");

        // TODO: Validate data

        $this->internshipService->createInternship(
            $title,
            $description,
            $ownerId,
            // $organizationId,
            // $isPublished,
            1,
            true,
        );
        return $this->redirect("/internship-program/internships");
    }
}