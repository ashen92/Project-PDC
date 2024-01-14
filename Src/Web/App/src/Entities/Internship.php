<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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

    #[ORM\ManyToOne(targetEntity: Partner::class, inversedBy: "internshipsCreated")]
    #[ORM\JoinColumn(name: "owner_user_id", referencedColumnName: "id")]
    private Partner $owner;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: "organization_id", referencedColumnName: "id")]
    private Organization $organization;

    /**
     * Many Internships have Many Students who applied to it.
     * @var Collection<int, Student>
     */
    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: "internshipsApplied")]
    #[ORM\JoinTable(name: "internship_applicants")]
    private Collection $applicants;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: "internships")]
    #[ORM\JoinColumn(name: "internship_cycle_id", referencedColumnName: "id")]
    private InternshipCycle $internshipCycle;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "boolean")]
    private bool $isPublished;

    public function __construct(string $title, string $description, Partner $owner, InternshipCycle $internshipCycle)
    {
        $this->title = $title;
        $this->description = $description;
        $this->owner = $owner;
        $this->organization = $owner->getOrganization();
        $owner->addToInternshipsCreated($this);
        $this->internshipCycle = $internshipCycle;
        $this->createdAt = new \DateTimeImmutable();
        $this->isPublished = true;
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

    public function getOwner(): Partner
    {
        return $this->owner;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getApplicants(): array
    {
        return $this->applicants->toArray();
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function addApplicant(Student $user): void
    {
        $this->applicants[] = $user;
        $user->addToInternshipsApplied($this);
    }

    public function removeApplicant(Student $user): void
    {
        $this->applicants->removeElement($user);
        $user->removeFromInternshipsApplied($this);
    }
}