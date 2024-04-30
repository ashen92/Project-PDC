<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateSessionDTO;
use App\DTOs\CreateSessionTitleDTO;
use App\Interfaces\IRepository;
use DateTimeImmutable;
use PDO;

class TechtalksRepository implements IRepository
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

    public function createSession(
        CreateSessionDTO $dto,
    ): int {
        $sql = "INSERT INTO techtalks (
            techtalksessionnumber,
            startTime,
            endTime,
            sessionLocation
            ) VALUES (
            :techtalksessionnumber,
            :startTime,
            :endTime,
            :sessionLocation
            )";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "techtalksessionnumber" => $dto->techtalksessionnumber,
            "startTime" => $dto->startTime->format($this::DATE_TIME_FORMAT),
            "endTime" => $dto->endTime->format($this::DATE_TIME_FORMAT),
            "sessionLocation" => $dto->sessionLocation
            //"description" => $dto->description,

        ]);

        $sessionId = (int) $this->pdo->lastInsertId();

        return $sessionId;
    }

    public function createSessionTitle(
        CreateSessionTitleDTO $dto, int $sessionId, int $userId
    ): void {
        $sql = "UPDATE techtalks 
                SET
                companyname = :companyname,
                title = :title,
                description = :description,
                created_by_user_id = :userId
                WHERE
                id = $sessionId";
        /* var_dump($sql);die(); */
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "userId" => $userId,
            "companyname" => $dto->companyname,
            "title" => $dto->title,
            "description" => $dto->description

        ]);

        //$sessionId = (int) $this->pdo->lastInsertId();

        //return $sessionId;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM techtalks WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function sessionExists(DateTimeImmutable $startTime, string $sessionLocation): bool
    {
        $sql = "SELECT COUNT(*) FROM techtalks WHERE startTime = :startTime AND sessionLocation = :sessionLocation";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':startTime' => $startTime->format($this::DATE_TIME_FORMAT),
            ':sessionLocation' => $sessionLocation
        ]);
        $count = $statement->fetchColumn();
        return $count > 0;
    }

    public function addParticipantToSession(int $sessionId, int $userGroupId): void
    {
        $sql = "INSERT INTO session_participants (techtalks_id, usergroup_id) VALUES (:sessionId, :userGroupId)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'sessionId' => $sessionId,
            'userGroupId' => $userGroupId,
        ]);
    }

    public function updateParticipantToSession(int $eventId, int $userGroupId): void
    {
        $sql = "UPDATE session_participants SET usergroup_id=:userGroupId WHERE event_id=:eventId"; 
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'eventId' => $eventId,
            'userGroupId' => $userGroupId,
        ]);
    }

    public function updateSessions(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?string $sessionLocation = null,
        ?DateTimeImmutable $startTime = null,
        ?DateTimeImmutable $endTime = null,
        //?array $participants = null
        
    ): bool {
        if ($title === null && $description === null && $sessionLocation === null && $startTime === null && $endTime === null) {
            return true;
        }

        $sql = 'UPDATE techtalks SET ';
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
        if ($sessionLocation !== null) {
            if (count($params) > 0) {
                $sql .= ', ';
            }
            $sql .= 'sessionLocation = :sessionLocation';
            $params['sessionLocation'] = $sessionLocation;
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

    public function getSession(int $id): array
    {

        $sql = "SELECT  *
                FROM    techtalks 
                WHERE   id=:id";


        $statement = $this->pdo->prepare($sql);

        $statement->bindParam(':id', $id);

        $statement->execute();

        $session = $statement->fetchAll(PDO::FETCH_ASSOC);

        $session['startTime'] = new DateTimeImmutable($session['startTime']);
        $session['endTime'] = new DateTimeImmutable($session['endTime']);

        return $session;
    }

    public function getSessions(DateTimeImmutable $startTime, DateTimeImmutable $endTime): array
    {
        $sql = "SELECT * FROM techtalks
                WHERE (startTime BETWEEN :startTime AND :endTime)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':startTime' => $startTime->format($this::DATE_TIME_FORMAT),
            ':endTime' => $endTime->format($this::DATE_TIME_FORMAT)
        ]);

        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sessions as &$session) {
            $session['startTime'] = new DateTimeImmutable($session['startTime']);
            $session['endTime'] = new DateTimeImmutable($session['endTime']);
        }

        return $sessions;
    }

    public function getSessionlist(): array
    {

        $sql = "SELECT  *
                FROM techtalks
                WHERE title IS NOT NULL";


        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        $session = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $session;
    }

    public function getSessionlistCompany($userId): array
    {

        $sql = "SELECT  *
                FROM techtalks
                WHERE  created_by_user_id = $userId";


        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        $session = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $session;
    }

    public function getSessionById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM techtalks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}