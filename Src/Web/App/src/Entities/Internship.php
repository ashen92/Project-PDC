<?php
declare(strict_types=1);

namespace App\Entities;

use App\Models\Internship\Status;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'internships')]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'internship_status')]
    private Status $status;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: 'internships')]
    #[ORM\JoinColumn(name: 'internship_cycle_id', referencedColumnName: 'id')]
    private InternshipCycle $internshipCycle;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id')]
    private User $createdBy;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id')]
    private Organization $organization;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $title,
        string $description,
        Status $status,
        InternshipCycle $internshipCycle,
        User $createdBy,
        Organization $organization,
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->internshipCycle = $internshipCycle;
        $this->createdBy = $createdBy;
        $this->organization = $organization;
        $this->createdAt = new DateTimeImmutable();
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

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }
}