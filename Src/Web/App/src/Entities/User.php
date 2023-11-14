<?php
declare(strict_types=1);

namespace App\Entities;

use App\Repositories\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $email;

    #[ORM\Column]
    private string $firstName;

    #[ORM\Column]
    private string $passwordHash;

    /**
     * Many Users have Many Groups.
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: "users")]
    private Collection $groups;

    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: "owner")]
    private Collection $internshipsCreated;

    /**
     * Many Users apply to Many Internships.
     * @var Collection<int, Internship>
     */
    #[ORM\ManyToMany(targetEntity: Internship::class, mappedBy: "applicants")]
    private Collection $internshipsApplied;

    #[ORM\OneToMany(targetEntity: UserRequirement::class, mappedBy: "user")]
    private Collection $assignedRequirements;

    public function __construct(string $email, string $firstName, string $passwordHash)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->passwordHash = $passwordHash;
        $this->groups = new ArrayCollection();
        $this->internshipsCreated = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function addToGroup(Group $group): void
    {
        $this->groups[] = $group;
    }

    public function addToInternshipsCreated(Internship $internship): void
    {
        $this->internshipsCreated[] = $internship;
    }

    public function addToInternshipsApplied(Internship $internship): void
    {
        $this->internshipsApplied[] = $internship;
    }
}