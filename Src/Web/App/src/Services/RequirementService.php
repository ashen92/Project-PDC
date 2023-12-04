<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\RequirementViewDTO;
use App\DTOs\UserRequirementCompletionDTO;
use App\DTOs\UserRequirementViewDTO;
use App\Entities\Requirement;
use App\Entities\UserRequirement;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IRequirementService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Contracts\Cache\CacheInterface;

class RequirementService implements IRequirementService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache,
        private IFileStorageService $fileStorageService
    ) {

    }

    public function getRequirements(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.id, r.name, r.description, r.type, r.startDate, r.endBeforeDate, r.repeatInterval")
            ->from("App\Entities\Requirement", "r");
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function getRequirement(int $id): ?RequirementViewDTO
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\Requirement', 'i');

        $sql = "SELECT r.* FROM requirements r WHERE r.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        $r = $query->getOneOrNullResult();
        if ($r === null) {
            return null;
        }

        return new RequirementViewDTO(
            $r->getId(),
            $r->getName(),
            $r->getDescription(),
            $r->getType(),
            $r->getStartDate(),
            $r->getEndBeforeDate(),
            $r->getRepeatInterval(),
            $r->getFulfillMethod(),
            $r->getAllowedFileTypes(),
            $r->getMaxFileSize(),
            $r->getMaxFileCount()
        );
    }

    public function createRequirement(CreateRequirementDTO $requirementDTO): void
    {
        $requirement = new Requirement($requirementDTO);
        $this->entityManager->persist($requirement);
        $this->entityManager->flush();
    }

    public function getUserRequirements(int $userId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.id, r.name, r.description, r.type, r.startDate, r.repeatInterval")
            ->from("App\Entities\UserRequirement", "ur")
            ->innerJoin("ur.requirement", "r")
            ->innerJoin("ur.user", "u")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function getUserRequirement(int $id): UserRequirementViewDTO|null
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\UserRequirement', 'i');

        $sql = "SELECT ur.* 
                FROM user_requirements ur 
                INNER JOIN requirements r ON ur.requirement_id = r.id
                INNER JOIN users u ON ur.user_id = u.id
                WHERE ur.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        $ur = $query->getOneOrNullResult();
        if ($ur === null) {
            return null;
        }

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\Requirement', 'i');
        $sql = "SELECT r.* 
                FROM user_requirements ur 
                INNER JOIN requirements r ON ur.requirement_id = r.id
                INNER JOIN users u ON ur.user_id = u.id
                WHERE ur.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        $r = $query->getOneOrNullResult();

        return new UserRequirementViewDTO(
            $ur->getId(),
            $r->getName(),
            $r->getDescription(),
            $ur->getStartDate(),
            $ur->getEndDate(),
            $ur->getCompletedAt(),
            $ur->getStatus(),
            $r->getFulfillMethod(),
            $r->getAllowedFileTypes(),
            $r->getMaxFileSize(),
            $r->getMaxFileCount()
        );
    }

    public function completeUserRequirement(UserRequirementCompletionDTO $urCompletionDTO): void
    {
        $response = $this->fileStorageService->upload($urCompletionDTO->files);
        $ur = $this->entityManager
            ->getRepository(UserRequirement::class)
            ->find($urCompletionDTO->requirementId);

        $filePaths = [];

        foreach ($response["properties"] as $fileProperty) {
            $filePaths[] = $fileProperty["filePath"];
        }

        $ur->setFilePaths($filePaths);
        $ur->setCompletedAt(new DateTime("now"));
        $ur->setStatus("completed");
        $this->entityManager->flush();
    }
}