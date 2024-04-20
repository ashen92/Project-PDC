<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventRepository;
use App\DTOs\CreateEventDTO;

readonly class EventService
{
    public function __construct(
        private EventRepository $eventRepository
    ) {
    }

    public function getEvents()
    {
    }

    public function getEventById(int $id)
    {
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