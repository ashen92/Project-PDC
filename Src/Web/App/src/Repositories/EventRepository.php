<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateEventDTO;
use App\Interfaces\IRepository;
use DateTime;
use DateTimeImmutable;
use PDO;

class EventRepository implements IRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function createEvent(
        CreateEventDTO $dto,
    ): int {
        $sql = "INSERT INTO events (
            title,
            startTime,
            endTime,
            eventLocation,
            description
        ) VALUES (
            :title,
            :startTime,
            :endTime,
            :eventLocation,
            :description
        )";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "title" => $dto->title,
            "startTime" => $dto->startTime->format($this::DATE_TIME_FORMAT),
            "endTime" => $dto->endTime->format($this::DATE_TIME_FORMAT),
            "eventLocation" => $dto->eventLocation,
            "description" => $dto->description

        ]);
        return (int) $this->pdo->lastInsertId();
    }
    public function getEvent(int $id): array
    {

        $sql = "SELECT  *
                FROM    events 
                WHERE   id=:id";


        $statement = $this->pdo->prepare($sql);

        $statement->bindParam(':id', $id);

        $statement->execute();

        $event = $statement->fetchAll(PDO::FETCH_ASSOC);

        $event['startTime'] = new DateTimeImmutable($event['startTime']);
        $event['endTime'] = new DateTimeImmutable($event['endTime']);

        return $event;
    }

    public function getEvents(DateTimeImmutable $startTime, DateTimeImmutable $endTime): array
    {
        $sql = "SELECT * FROM events ";
                //WHERE (eventDate BETWEEN :startTime AND :endTime)

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':startTime' => $startTime->format($this::DATE_TIME_FORMAT),
            ':endTime' => $endTime->format($this::DATE_TIME_FORMAT)
        ]);

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($events as &$event) {
            $event['startTime'] = new DateTimeImmutable($event['startTime']);
            $event['endTime'] = new DateTimeImmutable($event['endTime']);
        }

        return $events;
    }
}