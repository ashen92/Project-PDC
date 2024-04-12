<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\createInternshipDTO;
use App\Interfaces\IFileStorageService;
use App\Models\Internship;
use App\Models\InternshipProgram\createApplication;
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

        return $res;
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

    public function createApplication(createApplication $createApplication): bool
    {
        // TODO: Check if internship exists
        // TODO: Check if user exists and is a student

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

    public function getStudentsByJobRole(int $jobRoleId): array
    {
        return $this->internshipRepository->findStudentsByJobRole($jobRoleId);
    }
}