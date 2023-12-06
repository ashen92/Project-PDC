<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Event;
use App\Interfaces\IEventService;
use Doctrine\ORM\EntityManagerInterface;

class EventService implements IEventService {
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getEvents(): array {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from("App\Entities\Event", "e");

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getEventById(int $id): Event|null {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from("App\Entities\Event", "e")
            ->where("e.id = :id")
            ->setParameter("id", $id);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function createEvent(Event $event): void {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function editEvent(Event $event): void {

        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }
    public function deleteEvent(Event $event): void {

        $this->entityManager->remove($event); // Mark the event for removal.
        $this->entityManager->flush();
    }
}