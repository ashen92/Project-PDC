<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\Internship;

interface IInternshipService
{
    public function getInternships(): array;
    public function getInternshipsByUserId(int $userId): array;
    public function getInternshipById(int $id): ?Internship;
    public function deleteInternshipById(int $id): void;
    public function addInternship(string $title, string $description, int $userId): void;
    public function updateInternship(int $id, string $title, string $description): void;
    public function applyToInternship(int $internshipId, int $userId): void;
    public function getInternshipsBy(int|null $userId = null, string $searchQuery): array;
}