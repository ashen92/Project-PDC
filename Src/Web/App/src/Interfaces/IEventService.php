<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\Event;

interface IEventService
{
    public function getEvents(): array;
    public function getEventById(int $id): Event|null;
    public function addEvent(string $title, string $description): void;
}