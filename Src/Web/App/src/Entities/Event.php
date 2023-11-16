<?php
declare(strict_types=1);

namespace App\Entities;

use DateTime;
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

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private DateTime $startTime;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private DateTime $endTime;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $eventDate;

    #[ORM\Column(type: Types::TEXT)]
    private string $eventLocation;

    public function __construct(string $title, string $description, DateTime $startTime, DateTime $endTime, DateTime $eventDate, string $eventLocation)
    {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->eventDate = $eventDate;
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
        $dateTime = $this->startTime;
        return $dateTime->format('H:i:s');
    }

    public function setStartTime(bool|DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): string
    {
        $dateTime = $this->endTime;
        return $dateTime->format('H:i:s');
    }

    public function setEndTime(bool|DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getEventDate(): string
    {
        $dateTime = $this->eventDate;
        return $dateTime->format('Y-m-d');
    }

    public function setEventDate(bool|DateTime $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getEventLocation(): string
    {
        return $this->eventLocation;
    }

    public function setEventLocation(string $eventLocation): void
    {
        $this->eventLocation = $eventLocation;
    }
}