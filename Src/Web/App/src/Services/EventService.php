<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Event;
use App\Interfaces\IEventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EventService implements IEventService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
    }

    public function getEvents(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("e")
            ->from("App\Entities\Event", "e");

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getEventById(int $id): Event|null
    {
        return $this->entityManager->getRepository(Event::class)->getInternshipById($id);
    }

    public function addEvent(string $title, string $description): void
    {
        $event = new Event($title, $description);
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function deleteEventById(int $id): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->delete()
            ->from("App\Entities\Event", "e")
            ->where("e.id = :id")
            ->setParameter("id", $id);
        $queryBuilder->getQuery()->execute();
    }
}