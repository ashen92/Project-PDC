<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Internship;
use App\Models\Internship\Visibility;
use App\Models\InternshipProgram\CreateApplication;
use App\Models\InternshipSearchResult;
use App\Models\Organization;
use App\Models\Student;
use App\Repositories\InternshipRepository;

readonly class InternshipService
{
    public function __construct(
        private InternshipRepository $internshipRepository,
        private InternshipProgramService $internshipProgramService,
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
        $res['submittedApplicationsCount'] = $this->internshipRepository->countSubmittedApplications($i['internship_cycle_id'], $studentId);
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

    public function createApplication(int $cycleId, CreateApplication $createApplication): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        $maxApplications = $this->internshipProgramService->valueOfSetting('MaxInternshipApplications');
        $studentApplications = $this->internshipRepository->countSubmittedApplications($cycleId, $createApplication->userId);

        if ($studentApplications >= $maxApplications) {
            throw new \InvalidArgumentException('Maximum number of applications reached', 1001);
        }

        $fileUploadResponse = $this->fileStorageService->upload($createApplication->files);
        if (!$fileUploadResponse) {
            return false;
        }

        return $this->internshipRepository->createApplication(
            $createApplication->internshipId,
            $createApplication->userId,
            $fileUploadResponse
        );
    }

    public function removeApplication(int $applicationId, int $internshipId, int $userId): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

        return $this->internshipRepository->deleteApplication($applicationId, $internshipId, $userId);
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

    public function getJobRole(int $jobRoleId): array
    {
        return $this->internshipRepository->findJobRole($jobRoleId);
    }

    public function getJobRoles(int $cycleId): array
    {
        return $this->internshipRepository->findJobRoles($cycleId);
    }

    public function getJobRolesAppliedTo(int $cycleId, int $studentId): array
    {
        return $this->internshipRepository->findJobRolesAppliedTo($cycleId, $studentId);
    }

    public function getStudentsByJobRole(int $jobRoleId): array
    {
        return $this->internshipRepository->findStudentsByJobRole($jobRoleId);
    }

    public function applyToJobRole(int $cycleId, int $jobRoleId, int $studentId): bool
    {
        $maxApplications = $this->internshipProgramService->valueOfSetting('MaxJobRoleApplications');
        $studentApplications = $this->internshipRepository->countSelectedJobRoles($cycleId, $studentId);

        if ($studentApplications >= $maxApplications) {
            throw new \InvalidArgumentException('Maximum number of job roles reached', 1002);
        }

        return $this->internshipRepository->applyToJobRole($jobRoleId, $studentId);
    }

    public function removeFromJobRole(int $jobRoleId, int $studentId): bool
    {
        return $this->internshipRepository->removeFromJobRole($jobRoleId, $studentId);
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