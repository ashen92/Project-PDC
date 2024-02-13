<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\UpdateStudentUserDTO;
use App\Interfaces\IPasswordHasher;
use App\Models\Student;
use App\Models\User;
use App\Repositories\UserRepository;

readonly class AuthenticationService
{
    public function __construct(
        private UserRepository $userRepo,
        private IPasswordHasher $passwordHasher
    ) {
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !$this->passwordHasher->verifyPassword($password, $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }

    public function getStudentByStudentEmail(string $email): ?Student
    {
        return $this->userRepo->findStudentByStudentEmail($email);
    }

    public function getUserByActivationToken(string $token): ?User
    {
        return $this->userRepo->findByActivationToken($token);
    }

    public function generateActivationToken(User $user): string
    {
        $token = $user->generateActivationToken();
        $this->userRepo->updateUser($user);
        return $token;
    }

    public function resetActivationToken(User $user): void
    {
        $user->resetActivationToken();
        $this->userRepo->updateUser($user);
    }

    public function updateStudentUser(User $user, UpdateStudentUserDTO $createStudentDTO): void
    {
        $user->setFirstName($createStudentDTO->firstName);
        $user->setLastName($createStudentDTO->lastName);
        $user->setEmail($createStudentDTO->email);
        $user->setPasswordHash($this->passwordHasher->hashPassword($createStudentDTO->password));
        $user->activate();

        $this->userRepo->updateUser($user);
    }
}