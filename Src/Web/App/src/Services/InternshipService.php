<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Internship;
use App\Models\Internship\Visibility;
use App\Models\InternshipSearchResult;
use App\Models\Organization;
use App\Repositories\ApplicationRepository;
use App\Repositories\InternshipRepository;

readonly class InternshipService
{
    public function __construct(
        private InternshipRepository $internshipRepository,
        private InternshipProgramService $internshipProgramService,
        private IFileStorageService $fileStorageService,
        private ApplicationRepository $applicationRepository,
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
        ?array $filterByOrg,
        ?Internship\Visibility $filterByVisibility,
        ?bool $isApproved,
        ?int $numberOfResults,
        ?int $offsetBy,
        ?int $filterByCreatorUserId = null,
    ): array {

        // TODO: Check if internship cycle exists
        // TODO: Check if user exists

        $result = $this->internshipRepository->searchInternships(
            $cycleId,
            $searchQuery,
            $filterByOrg,
            $filterByVisibility,
            $isApproved,
            $numberOfResults,
            $offsetBy,
            $filterByCreatorUserId,
        );

        return $this->setOrgLogos($result);
    }

    /**
     * @return array<Organization>
     */
    public function getOrganizationsForSearchQuery(
        int $cycleId,
        ?string $searchQuery,
        ?Visibility $visibility,
        ?bool $isApproved
    ): array {
        // TODO: Check if internship cycle exists

        return $this->internshipRepository->getOrganizationsForSearchQuery(
            $cycleId,
            $searchQuery,
            $visibility,
            $isApproved
        );
    }

    /**
     * @return array<Internship>
     */
    public function getInternships(int $cycleId, int $ownerId): array
    {
        return $this->internshipRepository->findInternships($cycleId, $ownerId);
    }

    public function countInternships(
        int $cycleId,
        ?string $searchQuery,
        ?array $filterByOrg,
        ?Internship\Visibility $filterByVisibility,
        ?bool $isApproved,
        ?int $creatorUserId = null
    ): int {

        // TODO: Check if internship cycle exists

        return $this->internshipRepository->count(
            $cycleId,
            $searchQuery,
            $filterByOrg,
            $filterByVisibility,
            $isApproved,
            $creatorUserId
        );
    }

    public function getInternshipDetailsForStudent(int $internshipId, int $studentId): array
    {
        $i = $this->internshipRepository->findInternshipDetailsForStudent($internshipId, $studentId);

        $res['id'] = $i['id'];
        $res['title'] = $i['title'];
        $res['description'] = $i['description'];
        $res['applicationId'] = $i['application_id'] ?? null;
        $res['submittedApplicationsCount'] = $this->applicationRepository->countInternshipApplicationsByStudent($i['internship_cycle_id'], $studentId);
        $res['maximumApplicationsCount'] = $this->internshipProgramService->valueOfSetting('MaxInternshipApplications');
        return $res;
    }

    public function getApplications(int $internshipId): array
    {
        return $this->internshipRepository->findAllApplications($internshipId);
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

    public function getOrganizations(): array
    {
        return $this->internshipRepository->findOrganizations();
    }

    public function getJobRole(int $jobRoleId): array
    {
        return $this->internshipRepository->findJobRole($jobRoleId);
    }

    public function getJobRoles(int $cycleId): array
    {
        return $this->internshipRepository->findJobRoles($cycleId);
    }

    public function createJobRole(int $cycleId, string $jobRoleName): bool
    {
        return $this->internshipRepository->createJobRole($cycleId, $jobRoleName);
    }

    public function modifyJobRole(int $jobRoleId, string $name): bool
    {
        return $this->internshipRepository->modifyJobRole($jobRoleId, $name);
    }

    public function deleteJobRole(int $jobRoleId): bool
    {
        return $this->internshipRepository->deleteJobRole($jobRoleId);
    }

    public function approveInternship(int $internshipId): bool
    {
        return $this->internshipRepository->approveInternship($internshipId);
    }

    public function undoApproveInternship(int $internshipId): bool
    {
        return $this->internshipRepository->undoApproveInternship($internshipId);
    }
}