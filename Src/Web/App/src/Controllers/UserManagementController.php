<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserManagementHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserManagementController
{
    public function __construct(
        private UserManagementHandler $userMgmtHandler,
    ) {
    }

    #[Route('/users', methods: ['PUT'])]
    public function updateUsers(Request $request): Response
    {
        #region Validation

        $content = $request->getContent();
        if (empty($content)) {
            return new Response(null, 400);
        }
        if (!json_validate($content)) {
            return new Response(null, 400);
        }

        $decoded = json_decode($content);

        foreach ($decoded as $i) {
            if (!property_exists($i, 'id')) {
                return new Response(null, 400);
            }

            if (!is_int($i->id)) {
                return new Response(null, 400);
            }

            $ID_MAX_INT = 2147483647;
            $ID_MIN_INT = 1;
            if ($i->id > $ID_MAX_INT || $i->id < $ID_MIN_INT) {
                return new Response(null, 400);
            }

            if (property_exists($i, 'isActive') && (!is_bool($i->isActive) || $i->isActive === true)) {
                return new Response(null, 400);
            }

            if (property_exists($i, 'isDisabled') && !is_bool($i->isDisabled)) {
                return new Response(null, 400);
            }
        }

        #endregion

        if ($this->userMgmtHandler->updateUsers($decoded)) {
            return new Response(null, 204);
        }
        return new Response(null, 404);
    }
}