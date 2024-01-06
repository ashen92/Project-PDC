<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateStudentUserDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\User;
use App\Exceptions\UserExistsException;
use App\Interfaces\IEmailService;
use App\Interfaces\IPasswordHasher;
use App\Interfaces\IUserService;
use App\Models\UserInviteEmail;
use App\Repositories\UserRepository;

class UserService implements IUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private IPasswordHasher $passwordHasher,
        private IEmailService $emailService
    ) {
    }

    public function createUser(CreateUserDTO $userDTO): User
    {
        $user = null;
        if ($userDTO->userType == "student") {
            $user = $this->getUserByStudentEmail($userDTO->studentEmail);
        } else {
            $user = $this->getUserByEmail($userDTO->email);
        }

        if ($user === null) {
            $user = $this->userRepository->createUser($userDTO);

            if ($userDTO->userType != "student" || ($userDTO->userType == "student" && $userDTO->sendEmail !== null)) {
                $mail = null;

                if ($userDTO->userType == "student") {
                    $mail = new UserInviteEmail(
                        $user->getStudentEmail(),
                        $user->getFullName(),
                        $user->generateActivationToken()
                    );
                } else {
                    $mail = new UserInviteEmail(
                        $user->getEmail(),
                        $user->getFirstName(),
                        $user->generateActivationToken()
                    );
                }

                $this->userRepository->save($user);

                $this->emailService->sendEmail($mail);
            }
            return $user;
        }
        throw new UserExistsException();
    }

    public function createStudentUser(CreateStudentUserDTO $createStudentDTO)
    {
        $user = $this->userRepository->find($createStudentDTO->id);

        $user->setFirstName($createStudentDTO->firstName);
        $user->setLastName($createStudentDTO->lastName);
        $user->setEmail($createStudentDTO->email);
        $user->setPasswordHash($this->passwordHasher->hashPassword($createStudentDTO->password));
        $user->setIsActive(true);
        $user->setActivationToken(null);
        $user->setActivationTokenExpiresAt(null);

        $this->userRepository->save($user);
    }

    /**
     * @return array An array of strings
     */
    public function getUserRoles(int $userId): array
    {
        return $this->userRepository->findUserRoles($userId);
    }

    public function hasRole(int $userId, string $role): bool
    {
        if ($role == "")
            return true;
        $roles = $this->userRepository->findUserRoles($userId);
        if (in_array($role, $roles))
            return true;
        return false;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUserByStudentEmail(string $email): ?User
    {
        return $this->userRepository->findByStudentEmail($email);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->userRepository->findByActivationToken($token);
    }

    public function saveUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function getManagedUsers(int $userId): array
    {
        return $this->userRepository->findManagedUsers($userId);
    }
}