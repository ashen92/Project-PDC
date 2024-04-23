<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventRepository;
use App\DTOs\CreateEventDTO;
use DateTimeImmutable;

readonly class EventService
{
    public function __construct(
        private EventRepository $eventRepository
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

    public function getEventById(int $id)
    {
        //return $this->eventRepository->getEventById($id);
    }

    public function createEvent(CreateEventDTO $dto): void
    {
        $this->eventRepository->createEvent($dto);
    }

    public function editEvent($event): void
    {
    }
    public function deleteEvent($event): void
    {
    }
}