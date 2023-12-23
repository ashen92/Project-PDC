<?php
declare(strict_types=1);

namespace App\Services;

use App\DTOs\InternshipDTO;
use App\DTOs\InternshipListViewDTO;
use App\Entities\Internship;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IInternshipService;
use App\Repositories\InternshipRepository;
use App\Repositories\UserRepository;

class InternshipService implements IInternshipService
{
    public function __construct(
        private InternshipRepository $internshipRepository,
        private UserRepository $userRepository,
        private InternshipCycleService $internshipCycleService,
        private IFileStorageService $fileStorageService
    ) {
    }

    /**
     * Summary of mapToInternshipListViewDTOs
     * @param array $internships Array of Internship
     * @return array Array of InternshipListViewDTO
     */
    private function mapToInternshipListViewDTOs(array $internships): array
    {
        $result = [];

        foreach ($internships as $internship) {
            $company = $internship->getOrganization();
            $internshipView = new InternshipListViewDTO($internship, $company->getName());

            $logo = $this->fileStorageService->get($company->getLogoFilePath());
            if ($logo !== false) {
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

    public function getInternshipsBy(?int $iCycleId, ?int $ownerId, ?string $searchQuery): array
    {
        if ($iCycleId) {
            $criteria = [];

            if ($searchQuery) {
                $criteria["title"] = $searchQuery;
            }

            if ($ownerId) {
                $criteria["owner"] = $ownerId;
            }

            $internships = $this->internshipRepository->findAllBy($criteria);
            return $this->mapToInternshipListViewDTOs($internships);
        }
        return [];
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

    public function addInternship(InternshipDTO $dto): void
    {
        $user = $this->userRepository->find($dto->ownerId);
        $internshipCycle = $this->internshipCycleService->getLatestActiveInternshipCycle();
        $internship = new Internship($dto->title, $dto->description, $user, $internshipCycle);
        $this->internshipRepository->save($internship);
    }

    public function updateInternship(int $id, string $title, string $description): void
    {
        $internship = $this->internshipRepository->find($id);
        $internship->setTitle($title);
        $internship->setDescription($description);
        $this->internshipRepository->save($internship);
    }

    public function applyToInternship(int $internshipId, int $userId): void
    {
        $internship = $this->internshipRepository->find($internshipId);
        $user = $this->userRepository->find($userId);
        $internship->addApplicant($user);
        $this->internshipRepository->save($internship);
    }
}