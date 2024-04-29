<?php
declare(strict_types=1);

namespace DB\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "techtalks")]
class Techtalks
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
    private string $sessionLocation;

    #[ORM\ManyToMany(targetEntity: UserGroup::class)]
    //#[ORM\JoinColumn(name: 'usergroup_id', referencedColumnName: 'id')]
    #[ORM\JoinTable(name: 'session_participants')]
    private Collection $participants;

    public function __construct(string $title, string $description, DateTimeImmutable $startTime, DateTimeImmutable $endTime, string $sessionLocation)
    {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->sessionLocation = $sessionLocation;
        $this->participants = new ArrayCollection();
    }


}