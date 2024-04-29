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
            "description" => $dto->description

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

    public function updateParticipantToEvent(int $eventId, int $userGroupId): void
    {
        $sql = "UPDATE event_participants SET usergroup_id=:userGroupId WHERE event_id=:eventId"; 
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'eventId' => $eventId,
            'userGroupId' => $userGroupId,
        ]);
    }

    public function updateEvents(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?string $eventLocation = null,
        ?DateTimeImmutable $startTime = null,
        ?DateTimeImmutable $endTime = null,
        //?array $participants = null
        
    ): bool {
        if ($title === null && $description === null && $eventLocation === null && $startTime === null && $endTime === null) {
            return true;
        }

        $sql = 'UPDATE events SET ';
        $params = [];
        if ($title !== null) {
            $sql .= 'title = :title';
            $params['title'] = $title;
        }
        if ($description !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'description = :description';
            $params['description'] = $description;
        }
        if ($eventLocation !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'eventLocation = :eventLocation';
            $params['eventLocation'] = $eventLocation;
        }
        if ($startTime !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'startTime = :startTime';
            $params['startTime'] = $startTime->format($this::DATE_TIME_FORMAT);
        }
        if ($endTime !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'endTime = :endTime';
            $params['endTime'] = $endTime->format($this::DATE_TIME_FORMAT);
        }
        /* if ($participants !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'participants = :participants';
            $params['participants'] = $participants;
        } */
       
        $sql .= ' WHERE id = :id';
        $params['id'] = $id;
        var_dump($sql);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
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