<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Internship;
use App\Interfaces\IFileStorageService;
use App\Interfaces\IInternshipService;
use App\Models\InternshipView;
use App\Repositories\InternshipRepository;
use Doctrine\ORM\EntityManagerInterface;

class InternshipService implements IInternshipService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InternshipRepository $internshipRepository,
        private IFileStorageService $fileStorageService
    ) {
    }

    /**
     * Summary of mapToInternshipViews
     * @param array Array of Internship
     * @return array Array of InternshipView
     */
    private function mapToInternshipViews(array $internships): array
    {
        $result = [];

        foreach ($internships as $internship) {
            $company = $internship->getOwner()->getOrganization();
            $internshipView = new InternshipView($internship, $company->getName());

            $logo = $this->fileStorageService->get($company->getLogoFilePath());
            if ($logo !== false) {
                $internshipView->organizationLogo = "data:{$logo['mimeType']};base64," . base64_encode($logo["content"]);
            }

            $result[] = $internshipView;
        }

        return $result;
    }

    public function getInternships(): array
    {
        $internships = $this->internshipRepository->findAll();
        return $this->mapToInternshipViews($internships);
    }

    public function getInternshipsBy(string $searchQuery, ?int $ownerId = null): array
    {
        $internships = $this->internshipRepository->findByTitleAndOwner($searchQuery, $ownerId);
        return $this->mapToInternshipViews($internships);
    }

    public function getInternshipsByUserId(int $userId): array
    {
        return $this->mapToInternshipViews($this->internshipRepository->findByOwner($userId));
    }

    public function getInternshipById(int $id): ?Internship
    {
        return $this->internshipRepository->find($id);
    }

    public function deleteInternshipById(int $id): void
    {
        $this->internshipRepository->delete($id);
    }

    public function addInternship(string $title, string $description, int $userId): void
    {
        $user = $this->entityManager->getReference("App\Entities\User", $userId);
        $internshipCycle = $this->entityManager->getReference("App\Entities\InternshipCycle", 1);
        $internship = new Internship($title, $description, $user, $internshipCycle);
        $this->entityManager->persist($internship);
        $this->entityManager->flush();
    }

    public function updateInternship(int $id, string $title, string $description): void
    {
        $internship = $this->entityManager->getReference("App\Entities\Internship", $id);
        $internship->setTitle($title);
        $internship->setDescription($description);
        $this->entityManager->flush();
    }

    public function applyToInternship(int $internshipId, int $userId): void
    {
        $internship = $this->entityManager->getReference("App\Entities\Internship", $internshipId);
        $user = $this->entityManager->getReference("App\Entities\User", $userId);
        $internship->addApplicant($user);
        $this->entityManager->flush();
    }
}