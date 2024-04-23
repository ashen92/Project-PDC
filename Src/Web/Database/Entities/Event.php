<?php
declare(strict_types=1);

namespace DB\Entities;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
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

    /* #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $eventDate;*/

    #[ORM\Column]
    private string $eventLocation;

    public function __construct(string $title, string $description, DateTimeImmutable $startTime, DateTimeImmutable $endTime, DateTimeImmutable $eventDate, string $eventLocation)
    {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        //$this->eventDate = $eventDate;
        $this->eventLocation = $eventLocation;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setTitle(string $titled): void
    {
        $this->title = $titled;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStartTime(): string
    {
        $DateTimeImmutable = $this->startTime;
        return $DateTimeImmutable->format('H:i:s');
    }

    public function setStartTime(DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): string
    {
        $DateTimeImmutable = $this->endTime;
        return $DateTimeImmutable->format('H:i:s');
    }

    public function setEndTime(DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
    }

    /* public function getEventDate(): string
    {
        $DateTimeImmutable = $this->eventDate;
        return $DateTimeImmutable->format('Y-m-d');
    } */

    /* public function setEventDate(bool|DateTimeImmutable $eventDate): void
    {
        $this->eventDate = $eventDate;
    } */

    public function getEventLocation(): string
    {
        return $this->eventLocation;
    }

    public function setEventLocation(string $eventLocation): void
    {
        $this->eventLocation = $eventLocation;
    }
}