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
            ->select("e.id, e.title, e.description")
            ->from("Event", "e");

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getEventById(int $id): Event|null
    {
        return $this->entityManager->getRepository(Event::class)->getInternshipById($id);
    }

}