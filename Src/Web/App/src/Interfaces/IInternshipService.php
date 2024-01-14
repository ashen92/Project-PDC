<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\InternshipListViewDTO;
use App\Entities\Internship;
use App\Entities\Organization;
use App\Models\Student;

interface IInternshipService
{
    public function getInternship(int $id, ?int $internshipCycleId = null): ?\App\Models\Internship;

    /**
     * @return array<InternshipListViewDTO>
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
     * @return array<Student>
     */
    public function getApplications(int $internshipId): array;

    /**
     * @param array<Internship> $internships
     * @return array<Organization>
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
    public function apply(int $internshipId, int $userId): bool;
    public function undoApply(int $internshipId, int $userId): bool;
    public function hasAppliedToInternship(int $internshipId, int $userId): bool;
}