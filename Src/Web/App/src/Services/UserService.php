<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\StudentUserViewDTO;
use App\DTOs\UserActivationTokenDTO;
use App\DTOs\UserViewDTO;
use App\Entities\User;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserService implements IUserService {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO) {
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
    public function getUserRoles(int $userId): array {
        // $cacheKey = "user_roles_" . $userId;

        // return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userId) {
        // $item->expiresAfter(3600);
        return $this->entityManager->getRepository(User::class)->getUserRoles($userId);
        // });
    }

    public function invalidateUserCache(int $userId): void {
        $this->invalidateUserRolesCache($userId);
    }

    public function invalidateUserRolesCache(int $userId): void {
        $this->cache->delete("user_roles_".$userId);
    }

    public function hasRole(int $userId, string $role): bool {
        if($role == "")
            return true;
        $roles = $this->getUserRoles($userId);
        if(in_array($role, $roles))
            return true;
        return false;
    }

    public function getUserByEmail(string $email): ?UserViewDTO {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
        if(!$user)
            return null;

        return new UserViewDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPasswordHash(),
            $user->getIsActive(),
            $user->getActivationToken(),
            $user->getActivationTokenExpiresAt()
        );
    }

    public function getUserByStudentEmail(string $email): ?StudentUserViewDTO {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["studentEmail" => $email]);
        if(!$user)
            return null;

        return new StudentUserViewDTO(
            $user->getStudentEmail(),
            $user->getFullName(),
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPasswordHash(),
            $user->getIsActive(),
            $user->getActivationToken(),
            $user->getActivationTokenExpiresAt()
        );
    }

    public function getUserByActivationToken(string $token): ?UserViewDTO {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["activationToken" => $token]);

        if(!$user)
            return null;

        return new UserViewDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPasswordHash(),
            $user->getIsActive(),
            $user->getActivationToken(),
            $user->getActivationTokenExpiresAt()
        );
    }

    public function saveActivationToken(UserActivationTokenDTO $dto): void {
        $user = $this->entityManager->getRepository(User::class)->find($dto->id);
        $user->setActivationToken($dto->activationToken);
        $user->setActivationTokenExpiresAt($dto->activationTokenExpiresAt);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}