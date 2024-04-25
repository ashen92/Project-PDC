<?php
declare(strict_types=1);

namespace DB\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "events")]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $startTime;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $endTime;

    #[ORM\Column]
    private string $eventLocation;

    #[ORM\ManyToMany(targetEntity: UserGroup::class)]
    //#[ORM\JoinColumn(name: 'usergroup_id', referencedColumnName: 'id')]
    #[ORM\JoinTable(name: 'event_participants')]
    private Collection $participants;

    public function __construct(string $title, string $description, DateTimeImmutable $startTime, DateTimeImmutable $endTime, DateTimeImmutable $eventDate, string $eventLocation)
    {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->eventLocation = $eventLocation;
        $this->participants = new ArrayCollection();
    }

    
}