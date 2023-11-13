<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\RequirementDTO;
use App\Entities\Requirement;
use App\Interfaces\IRequirementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class RequirementService implements IRequirementService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {

    }

    public function getRequirements(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.id, r.name")
            ->from("App\Entities\Requirement", "r");
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function createRequirement(RequirementDTO $requirementDTO): void
    {
        $requirement = new Requirement($requirementDTO);
        $this->entityManager->persist($requirement);
        $this->entityManager->flush();
    }
}