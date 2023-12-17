<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\InternshipDTO;
use App\Entities\Internship;

interface IInternshipService
{
    public function getInternships(): array;
    public function getInternshipsByUserId(int $userId): array;
    public function getInternshipById(int $id): ?Internship;
    public function deleteInternshipById(int $id): void;
    public function addInternship(InternshipDTO $internshipDTO): void;
    public function updateInternship(int $id, string $title, string $description): void;
    public function applyToInternship(int $internshipId, int $userId): void;
    public function getInternshipsBy(string $searchQuery, ?int $userId = null): array;
}