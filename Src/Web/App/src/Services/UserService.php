<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use App\Interfaces\IUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userId) {
            $item->expiresAfter(3600);
            return $this->entityManager->getRepository(User::class)->getUserRoles($userId);
        });
    }

    public function invalidateUserCache(int $userId): void
    {
        $this->invalidateUserRolesCache($userId);
    }

    public function invalidateUserRolesCache(int $userId): void
    {
        $this->cache->delete("user_roles_" . $userId);
    }

    public function hasRole(int $userId, string $role): bool
    {
        if ($role == "")
            return true;
        $roles = $this->getUserRoles($userId);
        if (in_array($role, $roles))
            return true;
        return false;
    }
}