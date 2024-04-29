<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateSessionDTO;
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
            title,
            startTime,
            endTime,
            sessionLocation,
            description
        ) VALUES (
            :title,
            :startTime,
            :endTime,
            :sessionLocation,
            :description
        )";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "title" => $dto->title,
            "startTime" => $dto->startTime->format($this::DATE_TIME_FORMAT),
            "endTime" => $dto->endTime->format($this::DATE_TIME_FORMAT),
            "sessionLocation" => $dto->sessionLocation,
            "description" => $dto->description,

        ]);

        $sessionId = (int) $this->pdo->lastInsertId();

        return $sessionId;
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
                FROM techtalks";


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