<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\TechtalksRepository;
use App\Repositories\UserRepository;
use App\Models\UserGroup;
use App\DTOs\CreateSessionDTO;
use DateTimeImmutable;

readonly class TechtalksService
{
    public function __construct(
        private TechtalksRepository $techtalksRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function getSessions(DateTimeImmutable $startTime, DateTimeImmutable $endTime): array
    {
        $sessions = $this->techtalksRepository->getSessions($startTime, $endTime);

        foreach ($sessions as &$session) {
            $session['allDay'] = false;
            $session['start'] = $session['startTime']->format('Y-m-d\TH:i:s');
            $session['end'] = $session['endTime']->format('Y-m-d\TH:i:s');
            unset($session['startTime'], $session['endTime']);
        }

        return $sessions;
    }

    public function getSessionlist(): array
    {
        $sessions = $this->techtalksRepository->getSessionlist();
        return $sessions;
    }

    public function getUserGroups(): array
    {
        $groups = $this->userRepository->findAllUserGroups();
        $eligibleGroups = [];
        foreach ($groups as $group) {
            if (str_contains(strtolower($group->getName()), 'admin')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'coordinator')) {
                continue;
            }
            if (str_contains(strtolower($group->getName()), 'partner')) {
                continue;
            }
            if (str_starts_with($group->getName(), UserGroup::AUTO_GENERATED_USER_GROUP_PREFIX)) {
                continue;
            }
            $eligibleGroups[] = $group;
        }
        return $eligibleGroups;
    }

    public function createSession(CreateSessionDTO $dto): void
    {
        $this->techtalksRepository->createSession($dto);

    }

    public function sessionExists(DateTimeImmutable $startTime, string $sessionLocation): bool
    {
        return $this->techtalksRepository->sessionExists($startTime, $sessionLocation);
    }

    public function deleteSession($id): bool
    {
        return $this->techtalksRepository->delete($id);
    }

    public function addParticipantToSession(int $sessionId, int $userGroupId): void
    {
        $this->techtalksRepository->addParticipantToSession($sessionId, $userGroupId);
    }

    public function getSessionById(int $id)
    {
        return $this->techtalksRepository->getSessionById($id);
    }


}

