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
        // $cacheKey = "internships";
        // return $this->cache->get($cacheKey, function (ItemInterface $item) {
        // $item->expiresAfter(3600);
        // return $internships;
        // });

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("i.id, i.title, i.description, u.firstName")
            ->from("App\Entities\Internship", "i")
            ->leftJoin("i.owner", "u");
        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getInternshipsByUserId(int $userId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("i.id, i.title, i.description, u.firstName")
            ->from("App\Entities\Internship", "i")
            ->leftJoin("i.owner", "u")
            ->where("u.id = :userId")
            ->setParameter("userId", $userId);
        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getInternshipById(int $id): Internship|null
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("i")
            ->from("App\Entities\Internship", "i")
            ->where("i.id = :id")
            ->setParameter("id", $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
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

    public function applyToInternship(int $internshipId, int $userId): void
    {
        $internship = $this->entityManager->getReference("App\Entities\Internship", $internshipId);
        $user = $this->entityManager->getReference("App\Entities\User", $userId);
        $internship->addApplicant($user);
        $this->entityManager->flush();
    }

    public function getInternshipsBy(int|null $userId = null, string $searchQuery): array
    {
        if ($userId === null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select("i")
                ->from("App\Entities\Internship", "i")
                ->where("LOWER(i.title) LIKE :searchQuery")
                ->setParameter("searchQuery", "%$searchQuery%");
            $query = $queryBuilder->getQuery();
            return $query->getArrayResult();
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select("i")
            ->from("App\Entities\Internship", "i")
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq("i.user", ":userId"),
                    $queryBuilder->expr()->like("LOWER(i.title)", ":searchQuery")
                )
            )
            // ->where("i.user = :userId")
            // ->andWhere("LOWER(i.title) LIKE :searchQuery")
            ->setParameter("userId", $userId)
            ->setParameter("searchQuery", "%$searchQuery%");
        $query = $queryBuilder->getQuery();
        return $query->getArrayResult();
    }
}