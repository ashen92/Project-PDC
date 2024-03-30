<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\UserRequirementFulfillmentDTO;
use App\Models\InternshipCycle;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;
use App\Security\Attributes\RequiredRole;
use App\Security\Identity;
use App\Security\Role;
use App\Services\RequirementService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Twig\Environment;

#[RequiredRole([
    Role::InternshipProgram_Admin,
    Role::InternshipProgram_Partner,
    Role::InternshipProgram_Student,
])]
#[Route('/internship-program/requirements')]
class RequirementsController extends PageControllerBase
{
    public function __construct(
        Environment $twig,
        private readonly RequirementService $requirementService
    ) {
        parent::__construct($twig);
    }

    #[Route([''], methods: ['GET'])]
    public function requirements(Request $request, Identity $identity, ?InternshipCycle $cycle): Response
    {
        if ($identity->hasRole(Role::InternshipProgram_Admin)) {
            return $this->render(
                'internship-program/requirements/home-admin.html',
                [
                    'section' => 'requirements',
                    'requirements' => $this->requirementService->getRequirements()
                ]
            );
        }

        $cycleId = $cycle->getId();
        if ($cycleId) {
            $userReq = $this->requirementService->getUserRequirements(
                $cycleId,
                userId: $request->getSession()->get('user_id')
            );
        } else {
            $userReq = [];
        }

        return $this->render(
            'internship-program/requirements/home.html',
            [
                'section' => 'requirements',
                'userRequirements' => $userReq,
            ]
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function requirement(Identity $identity, int $id): Response|RedirectResponse
    {
        if ($identity->hasRole(Role::InternshipProgram_Admin)) {
            $requirement = $this->requirementService->getRequirement($id);
            if ($requirement) {
                return $this->render(
                    'internship-program/requirements/requirement.html',
                    [
                        'section' => 'requirements',
                        'requirement' => $requirement
                    ]
                );
            }
        } else {
            $ur = $this->requirementService->getUserRequirement($id);
            if ($ur) {
                $r = $this->requirementService->getRequirement($ur->getRequirement());
                return $this->render(
                    'internship-program/requirements/user-requirement.html',
                    [
                        'section' => 'requirements',
                        'requirement' => $r,
                        'userRequirement' => $ur
                    ]
                );
            }
        }
        return $this->redirect('/internship-program/requirements');
    }

    #[Route('/create', methods: ['GET'])]
    public function requirementAddGET(): Response
    {
        return $this->render(
            'internship-program/requirements/create.html',
            ['section' => 'requirements']
        );
    }

    #[Route('/create/users', methods: ['GET'])]
    public function createRequirementSelectUsersGET(): Response
    {
        return $this->render(
            'internship-program/requirements/select-users.html',
            ['section' => 'requirements']
        );
    }

    #[Route('/create', methods: ['POST'])]
    public function requirementAddPOST(Request $request): RedirectResponse
    {
        $fileTypes = $request->get('file-types');
        if (!is_array($fileTypes)) {
            $fileTypes = [$fileTypes];
        }

        try {
            $repeatInterval = $request->get('repeat-interval');

            $requirementDTO = new CreateRequirementDTO(
                $request->get('name'),
                $request->get('description'),
                Type::tryFrom($request->get('type')),
                new DateTimeImmutable($request->get('start-date')),
                new DateTimeImmutable($request->get('end-before')),
                $repeatInterval ? RepeatInterval::tryFrom($repeatInterval) : null,
                FulFillMethod::tryFrom($request->get('fulfill-method')),
                $fileTypes,
                (int) $request->get('max-file-size'),
                (int) $request->get('max-file-count')
            );

            // TODO: Validate

        } catch (Throwable) {
            // TODO: Handle errors
            return $this->redirect('/internship-program/requirements');
        }

        $this->requirementService->createRequirement($requirementDTO);
        return $this->redirect('/internship-program/requirements');
    }

    #[Route('/complete', methods: ['POST'])]
    public function complete(Request $request): Response|RedirectResponse
    {
        $files = $request->files->get('files-to-upload');
        if ($files && !is_array($files)) {
            $files = [$files];
        }

        $textResponse = $request->get('text-response');

        $urCompletionDTO = new UserRequirementFulfillmentDTO(
            (int) $request->get('user-requirement-id'),
            $files,
            $textResponse,
        );
        // Validate DTO

        $this->requirementService->completeUserRequirement($urCompletionDTO);
        // todo
        // Handle errors

        return $this->redirect(
            '/internship-program/requirements/' . $urCompletionDTO->userRequirementId
        );
    }
}