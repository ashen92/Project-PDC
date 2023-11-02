<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Event;
use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function getEventById(int $id): Event|null
    {
        $query = $this->createQueryBuilder("e")->where("e.id = :id")->setParameter(":id", $id)->getQuery();
        return $query->setMaxResults(1)->getOneOrNullResult();
    }
    public function deleteEvent(int $id): void
    {
        $event = $this->getEventById($id);

        if ($event) {
            $this->_em->remove($event); // Mark the event for removal.
            $this->_em->flush(); // Persist the changes to delete the event.
        }
    }
}