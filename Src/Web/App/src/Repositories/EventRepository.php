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

    public function commit(): bool
    {
        return $this->pdo->commit();
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
            "description" => $dto->description,

        ]);

        $eventId = (int) $this->pdo->lastInsertId();
        /*  $userGroupIds = $dto->participants;

            if (!empty($userGroupIds)) {
                // Prepare SQL statement for inserting participants
                $sql2 = "INSERT INTO event_participants (event_id, usergroup_id) VALUES (:eventId, :userGroupId)";
                $statement2 = $this->pdo->prepare($sql2);

                // Execute insert statement for each participant
                foreach ($userGroupIds as $userGroupId) {
                    $statement2->execute([
                        "eventId" => $eventId,
                        "userGroupId" => $userGroupId,
                    ]);
                }
            } */

        return $eventId;
    }

    public function addParticipantToEvent(int $eventId, int $userGroupId): void
    {
        $sql = "INSERT INTO event_participants (event_id, usergroup_id) VALUES (:eventId, :userGroupId)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'eventId' => $eventId,
            'userGroupId' => $userGroupId,
        ]);
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
        $sql = "SELECT * FROM events
                WHERE (startTime BETWEEN :startTime AND :endTime)";

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

    /* public function getAllEvents()
    {
        $statement = $this->pdo->prepare("SELECT * FROM events ORDER BY date_time");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } */
}