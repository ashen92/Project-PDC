<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use App\Interfaces\IUserService;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements IUserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        return $this->entityManager->getRepository(User::class)->getUserRoles($userId);
    }
}