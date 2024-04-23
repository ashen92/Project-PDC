<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Controllers\ControllerBase;
use App\Security\AuthorizationService;
use App\Services\ApplicationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/api')]
class ApplicationsAPIController extends ControllerBase
{
    public function __construct(
        Environment $twig,
        AuthorizationService $authzService,
        private ApplicationService $applicationService
    ) {
        parent::__construct($twig, $authzService);
    }

    #[Route('/applicants/{id}/hire', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function hire(Request $request, int $id): Response
    {
        #region Validation

        # TODO: Validate id

        $content = $request->getContent();
        if (empty($content)) {
            return new Response(null, 400);
        }
        if (!json_validate($content)) {
            return new Response(null, 400);
        }

        $decoded = json_decode($content);

        $keys = ['organizationId', 'applicationId'];

        foreach ($keys as $key) {
            if (property_exists($decoded, $key)) {
                if (!is_int($decoded->$key)) {
                    return new Response(null, 400);
                }

                $ID_MAX_INT = 2147483647;
                $ID_MIN_INT = 1;
                if ($decoded->$key > $ID_MAX_INT || $decoded->$key < $ID_MIN_INT) {
                    return new Response(null, 400);
                }
            } else {
                $decoded->$key = null;
            }
        }

        #endregion

        $userId = $request->getSession()->get('user_id');

        if ($this->hasRole('Admin')) {
            if ($this->applicationService->hire($id, null, $userId, $decoded->applicationId, $decoded->organizationId)) {
                return new Response(null, 204);
            }
            // TODO: Handle errors
        } else {
            if ($this->applicationService->hire($id, $userId, null, $decoded->applicationId)) {
                return new Response(null, 204);
            }
            // TODO: Handle errors
        }
    }

    #[Route('/applicants/{id}/applications/{applicationId}/reject',
        requirements: ['id' => '\d+', 'applicationId' => '\d+'], methods: ['POST'])]
    public function rejectApplication(Request $request, int $id, int $applicationId): Response
    {
        # TODO: Validate data

        if ($this->applicationService->reject($id, $applicationId)) {
            return new Response(null, 204);
        }
        // TODO: Handle errors
        return new Response(null, 500);
    }

    #[Route('/applicants/{id}/applications/{applicationId}/status/reset',
        requirements: ['id' => '\d+', 'applicationId' => '\d+'], methods: ['POST'])]
    public function resetApplicationStatus(Request $request, int $id, int $applicationId): Response
    {
        # TODO: Validate data

        if ($this->applicationService->resetApplicationStatus($id, $applicationId)) {
            return new Response(null, 204);
        }
        // TODO: Handle errors
        return new Response(null, 500);
    }
}