<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Internship;
use App\Models\InternshipSearchResult;
use App\Models\Organization;
use App\Models\Student;

interface IInternshipService
{
    public function getInternship(int $id, ?int $cycleId = null): ?Internship;

    /**
     * @return array<InternshipSearchResult>
     */
    public function searchInternships(
        int $cycleId,
        ?string $searchQuery,
        ?int $ownerUserId,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array;

    /**
     * @return array<Organization>
     */
    public function searchInternshipsGetOrganizations(
        int $cycleId,
        ?string $searchQuery,
    ): array;

    public function getInternshipCount(int $cycleId, ?string $searchQuery, ?int $ownerUserId): int;

    /**
     * @return array<Student>
     */
    public function getApplications(int $internshipId): array;

    public function deleteInternship(int $id): bool;

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

    public function apply(int $internshipId, int $userId): bool;
    public function undoApply(int $internshipId, int $userId): bool;
    public function hasAppliedToInternship(int $internshipId, int $userId): bool;
}