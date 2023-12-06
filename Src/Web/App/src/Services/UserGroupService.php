<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\UserGroup;
use App\Interfaces\IUserGroupService;
use Doctrine\ORM\EntityManagerInterface;

class UserGroupService implements IUserGroupService {
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {

    }

    public function getUserGroupsForInternshipProgram(): array {
        return $this->entityManager->getRepository(UserGroup::class)->findAll();
    }
}