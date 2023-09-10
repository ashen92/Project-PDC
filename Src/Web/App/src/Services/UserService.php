<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use App\Interfaces\IUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class UserService implements IUserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        $cacheKey = "user_roles_" . $userId;

        $roles = $this->cache->get($cacheKey, function () use ($userId) {
            return $this->entityManager->getRepository(User::class)->getUserRoles($userId);
        });
        return $roles;
    }
}