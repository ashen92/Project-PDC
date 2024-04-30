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

    #[ORM\Column(nullable: true)]
    private string $companyname;

    #[ORM\Column(nullable: true)]
    private string $title;

    #[ORM\Column(type: 'text',nullable: true)]
    private string $description;

    #[ORM\Column(type: 'text')]
    private string $techtalksessionnumber;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $startTime;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $endTime;

    #[ORM\Column]
    private string $sessionLocation;

    #[ORM\ManyToMany(targetEntity: UserGroup::class)]
    #[ORM\JoinTable(name: 'session_participants')]
    private Collection $participants;

    public function __construct(string $companyname, string $title, string $description,string $techtalksessionnumber, DateTimeImmutable $startTime, DateTimeImmutable $endTime, string $sessionLocation)
    {
        $this->companyname = $companyname;
        $this->title = $title;
        $this->description = $description;
        $this->techtalksessionnumber = $techtalksessionnumber;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->sessionLocation = $sessionLocation;
        $this->participants = new ArrayCollection();
    }


}