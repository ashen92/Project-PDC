<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\InternshipListViewDTO;
use App\Entities\Internship;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IInternshipCycleService;
use App\Interfaces\IInternshipService;
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

    /**
     * @param array<Internship> $internships
     * @return array<InternshipListViewDTO>
     */
    private function mapToInternshipListViewDTOs(array $internships): array
    {
        $result = [];

        foreach ($internships as $internship) {
            $company = $internship->getOrganization();
            $internshipView = new InternshipListViewDTO($internship, $company->getName());

            $logo = $this->fileStorageService->get($company->getLogoFilePath());
            if ($logo) {
                $internshipView->organizationLogo = "data:{$logo['mimeType']};base64," . base64_encode($logo["content"]);
            }

            $result[] = $internshipView;
        }

        return $result;
    }

    public function getInternshipById(int $id, ?int $internshipCycleId = null): ?Internship
    {
        return $this->internshipRepository->find($id);
    }

    public function getInternshipsBy(
        ?int $iCycleId,
        ?int $ownerId,
        ?string $searchQuery,
        ?int $numberOfResults,
        ?int $offsetBy,
    ): array {
        if ($iCycleId) {
            $internships = $this->internshipRepository
                ->findAllBy($searchQuery, $ownerId, null, $numberOfResults, $offsetBy);
            return $this->mapToInternshipListViewDTOs($internships);
        }
        return [];
    }

    public function getNumberOfInternships(?int $iCycleId, ?int $ownerId, ?string $searchQuery): int
    {
        if ($iCycleId) {
            return $this->internshipRepository->count($searchQuery, $ownerId);
        }
        return 0;
    }

    public function getApplicants(int $internshipId): array
    {
        $internship = $this->internshipRepository->find($internshipId);
        return $internship->getApplicants();
    }

    public function getOrganizationsFrom(array $internships): array
    {
        $ids = array_map(
            fn(Internship $internship) => $internship->getOrganization()->getId(),
            $internships
        );
        return $this->internshipRepository->findOrganizations($ids);
    }

    public function deleteInternshipById(int $id): void
    {
        $this->internshipRepository->delete($id);
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
            $this->internshipCycleService->getLatestActiveInternshipCycle()->getId(),
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