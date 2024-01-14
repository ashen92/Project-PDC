<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Entities\Internship;

interface IInternshipService
{
    public function getInternshipById(int $id, ?int $internshipCycleId = null): ?Internship;

    /**
     * @return array<\App\DTOs\InternshipListViewDTO>
     */
    public function getInternshipsBy(
        ?int $iCycleId,
        ?int $ownerId,
        ?string $searchQuery,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array;

    public function getNumberOfInternships(?int $iCycleId, ?int $ownerId, ?string $searchQuery): int;

    /**
     * @return array<\App\Entities\Student>
     */
    public function getApplicants(int $internshipId): array;

    /**
     * @param array<Internship> $internships
     * @return array<\App\Entities\Organization>
     */
    public function getOrganizationsFrom(array $internships): array;

    public function deleteInternshipById(int $id): void;
    public function createInternship(
        string $title,
        string $description,
        int $ownerId,
        int $organizationId,
        bool $isPublished,
    ): void;
    public function updateInternship(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?bool $isPublished = null
    ): bool;
    public function applyToInternship(int $internshipId, int $userId): void;
    public function undoApplyToInternship(int $internshipId, int $userId): void;
    public function hasAppliedToInternship(int $internshipId, int $userId): bool;
}