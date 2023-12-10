<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\Entities\User;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements IUserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO)
    {
        $user = $this->entityManager->getRepository(User::class)->find($createStudentDTO->id);
        $user->setFirstName($createStudentDTO->firstName);
        $user->setLastName($createStudentDTO->lastName);
        $user->setEmail($createStudentDTO->email);
        $user->setPasswordHash($this->passwordHasher->hashPassword($createStudentDTO->password));
        $user->setIsActive(true);
        $user->setActivationToken(null);
        $user->setActivationTokenExpiresAt(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("r.name")
            ->from(User::class, "u")
            ->innerJoin("u.groups", "g")
            ->innerJoin("g.roles", "r")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);

        return $queryBuilder->getQuery()->getSingleColumnResult();
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

    public function getUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
    }

    public function getUserByStudentEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["studentEmail" => $email]);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(["activationToken" => $token]);
    }

    public function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}