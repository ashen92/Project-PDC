<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateEventDTO;
use App\Interfaces\IRepository;
use App\Mappers\EventMapper;
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
        return $eventId;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM events WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
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

    public function getEventlist(): array
    {

        $sql = "SELECT  *
                FROM events";


        $statement = $this->pdo->prepare($sql);

        //$statement->bindParam(':id', $id);

        $statement->execute();

        $event = $statement->fetchAll(PDO::FETCH_ASSOC);

        /* $event['startTime'] = new DateTimeImmutable($event['startTime']);
        $event['endTime'] = new DateTimeImmutable($event['endTime']); */

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

    public function getEventById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function findEventDetailForStudent(int $eventId, int $studentId): array
    {
        $sql = 'SELECT i.*, o.*, a.id as application_id
                FROM internships i
                LEFT JOIN organizations o ON i.organization_id = o.id
                LEFT JOIN applications a ON i.id = a.internship_id AND a.user_id = :studentId
                WHERE i.id = :internshipId';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'studentId' => $studentId,
            'internshipId' => $eventId,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /* public function getAllEvents()
    {
        $statement = $this->pdo->prepare("SELECT * FROM events ORDER BY date_time");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } */
}