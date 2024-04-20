<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateEventDTO;
use App\Interfaces\IRepository;
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
            eventDate,
            startTime,
            endTime,
            eventLocation,
            description
        ) VALUES (
            :title,
            :eventDate,
            :startTime,
            :endTime,
            :eventLocation,
            :description
        )";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            "title" => $dto->title,
            "eventDate" => $dto->eventDate->format($this::DATE_TIME_FORMAT),
            "startTime" => $dto->startTime->format($this::DATE_TIME_FORMAT),
            "endTime" => $dto->endTime->format($this::DATE_TIME_FORMAT),
            "eventLocation" => $dto->eventLocation,
            "description" => $dto->description

        ]);
        return (int) $this->pdo->lastInsertId();
    }
}