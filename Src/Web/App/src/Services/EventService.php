<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use App\Models\UserGroup;
use App\DTOs\CreateEventDTO;
use DateTimeImmutable;

readonly class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function getEvents(DateTimeImmutable $startTime, DateTimeImmutable $endTime): array
    {
        $events = $this->eventRepository->getEvents($startTime, $endTime);

        foreach ($events as &$event) {
            $event['allDay'] = false;
            $event['start'] = $event['startTime']->format('Y-m-d\TH:i:s');
            $event['end'] = $event['endTime']->format('Y-m-d\TH:i:s');
            unset($event['startTime'], $event['endTime']);
        }

        return $events;
    }

    public function getEventlist(): array
    {
        $events = $this->eventRepository->getEventlist();
        return $events;
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
    /* public function getAllEvents()
    {
        return $this->eventRepository->getAllEvents();
    } */

    public function getEventById(int $id)
    {
        return $this->eventRepository->getEventById($id);
    }

    public function createEvent(CreateEventDTO $dto): void
    {
        $eventId =  $this->eventRepository->createEvent($dto);
        $this->eventRepository->addParticipantToEvent($eventId, intval($dto->participants[0]));

    }

    public function updateEvents(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?string $eventLocation = null,
        ?DateTimeImmutable $startTime = null,
        ?DateTimeImmutable $endTime = null,
        ?array $participants = null
       
    ): bool {
        

        return $this->eventRepository->updateEvents($id, $title, $description, $eventLocation, $startTime, $endTime);
        return $this->eventRepository->updateParticipantToEvent($id, intval($participants[0]));
    }
    public function deleteEvent($id): bool
    {
        return $this->eventRepository->delete($id);
    }

    public function addParticipantToEvent(int $eventId, int $userGroupId): void
    {
        // Add participant to the specified event
        $this->eventRepository->addParticipantToEvent($eventId, $userGroupId);
    }


}