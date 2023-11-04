<?php
declare(strict_types=1);

namespace App\Entities;

use App\Repositories\InternshipRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InternshipRepository::class)]
#[ORM\Table(name: "internships")]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "internshipsCreated")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $owner;

    /**
     * Many Internships have Many Users who applied to it.
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "internshipsApplied")]
    #[ORM\JoinTable(name: "internships_applicants")]
    private Collection $applicants;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: "internships")]
    #[ORM\JoinColumn(name: "internshipcycle_id", referencedColumnName: "id")]
    private InternshipCycle $internshipCycle;

    public function __construct(string $title, string $description, User $owner, InternshipCycle $internshipCycle)
    {
        $this->title = $title;
        $this->description = $description;
        $this->owner = $owner;
        $owner->addToInternshipsCreated($this);
        $this->internshipCycle = $internshipCycle;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}