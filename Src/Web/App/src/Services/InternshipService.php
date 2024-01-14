<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IFileStorageService;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IInternshipService;
use App\Models\Internship;
use App\Models\InternshipSearchResult;
use App\Repositories\InternshipRepository;
use Override;

class InternshipService implements IInternshipService
{
    public function __construct(
        private readonly InternshipRepository $internshipRepository,
        private readonly IInternshipCycleService $internshipCycleService,
        private readonly IFileStorageService $fileStorageService
    ) {
    }

    #[Override] public function getInternship(int $id, ?int $cycleId = null): ?Internship
    {
        return $this->internshipRepository->findInternship($id);
    }

    /**
     * @param array<InternshipSearchResult> $internshipSearchResults
     * @return array<InternshipSearchResult>
     */
    private function setOrgLogos(array $internshipSearchResults): array
    {
        foreach ($internshipSearchResults as $result) {
            $orgLogo = $this->fileStorageService->get($result->organizationLogoFilePath);
            if ($orgLogo) {
                $result->setOrganizationLogo(
                    "data:{$orgLogo['mimeType']};base64," . base64_encode($orgLogo["content"])
                );
            }
        }
        return $internshipSearchResults;
    }

    #[Override] public function searchInternships(
        int $cycleId,
        ?string $searchQuery,
        ?int $ownerUserId,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array {

        // TODO: Check if internship cycle exists
        // TODO: Check if user exists

        $result = $this->internshipRepository->searchInternships(
            $cycleId,
            $ownerUserId,
            $searchQuery,
            $numberOfResults,
            $offsetBy,
        );

        return $this->setOrgLogos($result);
    }

    #[Override] public function searchInternshipsGetOrganizations(int $cycleId, ?string $searchQuery): array
    {
        // TODO: Check if internship cycle exists

        return $this->internshipRepository->searchInternshipsGetOrganizations(
            $cycleId,
            $searchQuery,
        );
    }

    public function getInternshipCount(int $cycleId, ?string $searchQuery, ?int $ownerUserId): int
    {

        // TODO: Check if internship cycle exists

        return $this->internshipRepository->count($cycleId, $searchQuery, $ownerUserId);
    }

    #[Override] public function getApplications(int $internshipId): array
    {
        return $this->internshipRepository->findAllApplications($internshipId);
    }

    public function deleteInternship(int $id): bool
    {
        return $this->internshipRepository->delete($id);
    }

    #[Override] public function createInternship(
        string $title,
        string $description,
        int $ownerId,
        int $organizationId,
        bool $isPublished,
    ): void {
        // TODO: Check if organization exists
        // TODO: Check if owner exists
        // TODO: Check if active internship cycle exists

        $this->internshipRepository->createInternship(
            $title,
            $description,
            $ownerId,
            $organizationId,
            $this->internshipCycleService->getLatestActiveCycle()->getId(),
            $isPublished
        );
    }

    #[Override] public function updateInternship(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?bool $isPublished = null
    ): bool {
        // TODO: Check if internship exists

        return $this->internshipRepository->updateInternship($id, $title, $description);
    }

    #[Override] public function apply(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->apply($internshipId, $userId);
    }

    #[Override] public function undoApply(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->undoApply($internshipId, $userId);
    }

    #[Override] public function hasAppliedToInternship(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->hasApplied($internshipId, $userId);
    }
}