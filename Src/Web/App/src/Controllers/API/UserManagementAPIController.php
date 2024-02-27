<?php
declare(strict_types=1);

namespace App\Controllers\API;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserManagementAPIController
{
    private const MAX_PAGE_SIZE = 50;

    public function __construct(
        private readonly UserService $userService
    ) {

    }

    #[Route('/users', methods: ['GET'])]
    public function users(Request $request): Response
    {
        $page = $request->query->getInt('page', 0);
        // TODO: Validate

        return new Response(
            json_encode(
                $this->userService->searchUsers(
                    self::MAX_PAGE_SIZE,
                    $page * self::MAX_PAGE_SIZE
                )
            ),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}