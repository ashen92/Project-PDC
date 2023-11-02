<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Internship;
use App\Interfaces\IInternshipService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InternshipService implements IInternshipService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
    }

    public function getInternships(): array
    {
        $cacheKey = "internships";

        // return $this->cache->get($cacheKey, function (ItemInterface $item) {
        // $item->expiresAfter(3600);
        return $this->entityManager->getRepository(Internship::class)->getInternships();
        // });
    }

    public function getInternshipById(int $id): Internship|null
    {
        return $this->entityManager->getRepository(Internship::class)->getInternshipById($id);
    }

    public function deleteInternshipById(int $id): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->delete()
            ->from("App\Entities\Internship", "i")
            ->where("i.id = :id")
            ->setParameter("id", $id);
        $queryBuilder->getQuery()->execute();
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
}