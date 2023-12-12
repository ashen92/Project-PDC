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
    public function getInternshipCycle(Requirement $requirement): InternshipCycle
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("ic")
            ->from(InternshipCycle::class, "ic")
            ->innerJoin("ic.requirements", "r")
            ->where("r.id = :requirementId")
            ->setParameter("requirementId", $requirement->getId());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getStudentUsers(InternshipCycle $internshipCycle): array
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\User', 'u');

        $queryBuilder = $this->entityManager->createNativeQuery(
            "SELECT u.*
            FROM user_groups ug
            JOIN user_group_membership ugm ON ug.id = ugm.usergroup_id
            JOIN users u ON ugm.user_id = u.id
            WHERE ug.id = (
                SELECT student_group_id
                FROM internship_cycles
                WHERE id = :internshipCycleId
            )",
            $rsm
        );

        $queryBuilder->setParameter("internshipCycleId", $internshipCycle->getId());
        return $queryBuilder->getResult();
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