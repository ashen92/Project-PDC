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
    public function getRequirement(int $id): ?Requirement
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entities\Requirement', 'i');

        $sql = "SELECT r.* FROM requirements r WHERE r.id = :id";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $id);
        return $query->getOneOrNullResult();
    }

    public function getRequirements(int $internshipCycleId): array
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

    public function getRequirementSubmissions(int $requirementId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("ur.id, ur.completedAt, ur.status, u.id as u_id, u.firstName, u.fullName, u.indexNumber, u.studentEmail")
            ->from("App\Entities\UserRequirement", "ur")
            ->innerJoin("ur.user", "u")
            ->innerJoin("ur.requirement", "r")
            ->where("r.id = :requirementId")
            ->andWhere("ur.status = :status")
            ->setParameter("requirementId", $requirementId)
            ->setParameter("status", "completed");

        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

    public function getUserRequirement(int $id): ?UserRequirement
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

    public function getUserRequirements(int $userId, int $internshipCycleId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("ur.id, r.id as r_id, r.name, r.description, r.requirementType, r.startDate, r.repeatInterval")
            ->from("App\Entities\UserRequirement", "ur")
            ->innerJoin("ur.requirement", "r")
            ->innerJoin("r.internshipCycle", "ic")
            ->innerJoin("ur.user", "u")
            ->where("u.id = :userId")
            ->andWhere("ic.id = :internshipCycleId")
            ->setParameter("userId", $userId)
            ->setParameter("internshipCycleId", $internshipCycleId);
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }

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
        return $internshipCycle->getStudentGroup()->getUsers()->toArray();

        // $queryBuilder = $this->entityManager->createNativeQuery(
        //     "SELECT u.*
        //     FROM user_groups ug
        //     JOIN user_group_membership ugm ON ug.id = ugm.usergroup_id
        //     JOIN users u ON ugm.user_id = u.id
        //     WHERE ug.id = (
        //         SELECT student_group_id
        //         FROM internship_cycles
        //         WHERE id = :internshipCycleId
        //     )",
        //     $rsm
        // );
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