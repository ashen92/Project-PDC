<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\InternshipDTO;
use App\Entities\Internship;

interface IInternshipService
{
    public function getInternshipById(int $id, ?int $internshipCycleId = null): ?Internship;
    public function getInternshipsBy(
        ?int $iCycleId,
        ?int $ownerId,
        ?string $searchQuery,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array;
    public function getNumberOfInternships(?int $iCycleId, ?int $ownerId, ?string $searchQuery): int;
    public function getApplicants(int $internshipId): array;
    public function getOrganizationsFrom(array $internships): array;
    public function deleteInternshipById(int $id): void;
    public function addInternship(InternshipDTO $internshipDTO): void;
    public function updateInternship(int $id, string $title, string $description): void;
    public function applyToInternship(int $internshipId, int $userId): void;
}