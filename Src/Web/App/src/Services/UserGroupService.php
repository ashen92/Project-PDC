<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\UserGroup;
use App\Interfaces\IUserGroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class UserGroupService implements IUserGroupService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {

    }

    public function getUserGroupsForInternshipProgram(): array
    {
        return $this->entityManager->getRepository(UserGroup::class)->findAll();
    }
}