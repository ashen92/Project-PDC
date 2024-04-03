<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Internship;
use App\Models\InternshipSearchResult;
use App\Models\Organization;
use App\Models\Student;
use App\Repositories\InternshipRepository;

readonly class InternshipService
{
    public function __construct(
        private InternshipRepository $internshipRepository,
        private InternshipProgramService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {
    }

    public function getInternship(int $id): ?Internship
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

    /**
     * @return array<InternshipSearchResult>
     */
    public function searchInternships(
        int $cycleId,
        ?string $searchQuery,
        ?array $filterByOrgIds,
        ?array $filterByStatuses,
        ?int $numberOfResults,
        ?int $offsetBy,
        ?int $filterByCreatorUserId = null,
    ): array {

        // TODO: Check if internship cycle exists
        // TODO: Check if user exists

        $result = $this->internshipRepository->searchInternships(
            $cycleId,
            $searchQuery,
            $filterByOrgIds,
            $filterByStatuses,
            $numberOfResults,
            $offsetBy,
            $filterByCreatorUserId,
        );

        return $this->setOrgLogos($result);
    }

    /**
     * @return array<Organization>
     */
    public function searchInternshipsGetOrganizations(int $cycleId, ?string $searchQuery): array
    {
        // TODO: Check if internship cycle exists

        return $this->internshipRepository->searchInternshipsGetOrganizations(
            $cycleId,
            $searchQuery,
        );
    }

    /**
     * @return array<Internship>
     */
    public function getInternships(int $cycleId, int $ownerId): array
    {
        return $this->internshipRepository->findInternships($cycleId, $ownerId);
    }

    public function getInternshipCount(int $cycleId, ?string $searchQuery, ?int $ownerUserId): int
    {

        // TODO: Check if internship cycle exists

        return $this->internshipRepository->count($cycleId, $searchQuery, $ownerUserId);
    }

    /**
     * @return array<Student>
     */
    public function getApplications(int $internshipId): array
    {
        $applications = $this->internshipRepository->findAllApplications($internshipId);
        $internship = $this->internshipRepository->findInternship($internshipId);

        foreach ($applications as &$application) {
            $application["isApplicantAvailable"] = $application["isApplicantAvailable"] === 1;
        }

        $res['id'] = $internship->getId();
        $res['title'] = $internship->getTitle();
        $res['applications'] = $applications;

        return $res;
    }

    public function deleteInternship(int $id): bool
    {
        return $this->internshipRepository->delete($id);
    }

    public function createInternship(
        int $cycleId,
        createInternshipDTO $dto,
    ): void {
        // TODO: Check if organization exists
        // TODO: Check if owner exists
        // TODO: Check if active internship cycle exists

        $this->internshipRepository->createInternship($cycleId, $dto);
    }

    public function updateInternship(
        int $id,
        ?string $title = null,
        ?string $description = null,
        ?bool $isPublished = null
    ): bool {
        // TODO: Check if internship exists

        return $this->internshipRepository->updateInternship($id, $title, $description);
    }

    public function apply(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->apply($internshipId, $userId);
    }

    public function undoApply(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->undoApply($internshipId, $userId);
    }

    public function hasAppliedToInternship(int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->hasApplied($internshipId, $userId);
    }

    public function getOrganizations(): array
    {
        return $this->internshipRepository->findOrganizations();
    }
}