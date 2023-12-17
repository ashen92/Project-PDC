<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateRequirementDTO;
use App\DTOs\CreateUserRequirementDTO;
use App\Entities\InternshipCycle;
use App\Entities\Requirement;
use App\Entities\User;
use App\Entities\UserRequirement;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class RequirementRepository extends Repository
{
    public function findRequirement(int $id): ?Requirement
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\Requirement', 'i');

        $sql = "SELECT r.* FROM requirements r WHERE r.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        return $query->getOneOrNullResult();
    }

    public function findAllRequirements(int $internshipCycleId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.id, r.name, r.description, r.requirementType, r.startDate, r.endBeforeDate, r.repeatInterval")
            ->from("App\Entities\Requirement", "r")
            ->innerJoin("r.internshipCycle", "ic")
            ->where("ic.id = :internshipCycleId")
            ->setParameter("internshipCycleId", $internshipCycleId);

        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function findUserRequirement(int $id): ?UserRequirement
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
        return $query->getOneOrNullResult();
    }

    public function findAllUserRequirements(array $criteria): array
    {
        return $this->entityManager->getRepository(UserRequirement::class)->findBy($criteria);
    }

    public function createRequirement(
        CreateRequirementDTO $requirementDTO,
        int $internshipCycleId
    ): Requirement {
        $requirement = new Requirement($requirementDTO);
        $internshipCycle = $this->entityManager->find(
            InternshipCycle::class,
            $internshipCycleId
        );
        $requirement->setInternshipCycle($internshipCycle);
        $this->entityManager->persist($requirement);
        $this->entityManager->flush();
        return $requirement;
    }

    public function createUserRequirement(
        Requirement $requirement,
        User $user
    ): UserRequirement {
        $userRequirement = new UserRequirement($user, $requirement);
        $this->entityManager->persist($userRequirement);
        $this->entityManager->flush();
        return $userRequirement;
    }

    public function createUserRequirementFromDTO(
        Requirement $requirement,
        User $user,
        CreateUserRequirementDTO $userRequirementDTO
    ): UserRequirement {
        $userRequirement = new UserRequirement($user, $requirement);
        $userRequirement->setStartDate($userRequirementDTO->startDate);
        $userRequirement->setEndDate($userRequirementDTO->endDate);
        $userRequirement->setStatus($userRequirementDTO->status);
        $this->entityManager->persist($userRequirement);
        $this->entityManager->flush();
        return $userRequirement;
    }

    public function saveUserRequirement(UserRequirement $ur): void
    {
        $this->entityManager->persist($ur);
        $this->entityManager->flush();
    }
}