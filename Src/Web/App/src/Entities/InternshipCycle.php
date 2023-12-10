<?php
declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "internship_cycles")]
class InternshipCycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $endedAt;

    #[ORM\Column(type: "date", nullable: true)]
    private ?DateTime $collectionStartDate;

    #[ORM\Column(type: "date", nullable: true)]
    private ?DateTime $collectionEndDate;

    #[ORM\Column(type: "date", nullable: true)]
    private ?DateTime $applicationStartDate;

    #[ORM\Column(type: "date", nullable: true)]
    private ?DateTime $applicationEndDate;

    #[ORM\OneToOne(targetEntity: UserGroup::class)]
    #[ORM\JoinColumn(name: "student_group_id", referencedColumnName: "id")]
    private UserGroup $studentGroup;

    #[ORM\OneToOne(targetEntity: UserGroup::class)]
    #[ORM\JoinColumn(name: "partner_group_id", referencedColumnName: "id")]
    private UserGroup $partnerGroup;

    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'internshipCycle')]
    private Collection $internships;

    #[ORM\OneToMany(targetEntity: Requirement::class, mappedBy: 'internshipCycle')]
    private Collection $requirements;

    public function __construct()
    {
        $this->createdAt = new DateTime("now");
        $this->groups = new ArrayCollection();
        $this->internships = new ArrayCollection();
        $this->requirements = new ArrayCollection();
    }

    public function end(): void
    {
        $this->endedAt = new DateTime("now");
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    public function getCollectionStartDate(): ?DateTime
    {
        return $this->collectionStartDate;
    }

    public function getCollectionEndDate(): ?DateTime
    {
        return $this->collectionEndDate;
    }

    public function getApplicationStartDate(): ?DateTime
    {
        return $this->applicationStartDate;
    }

    public function getApplicationEndDate(): ?DateTime
    {
        return $this->applicationEndDate;
    }

    public function getPartnerGroup(): UserGroup
    {
        return $this->partnerGroup;
    }

    public function getStudentGroup(): UserGroup
    {
        return $this->studentGroup;
    }

    public function getPartnerUserGroupName(): string
    {
        return $this->partnerGroup->getName();
    }

    public function getStudentUserGroupName(): string
    {
        return $this->studentGroup->getName();
    }

    public function setCollectionStartDate(DateTime $collectionStartDate): void
    {
        $this->collectionStartDate = $collectionStartDate;
    }

    public function setCollectionEndDate(DateTime $collectionEndDate): void
    {
        $this->collectionEndDate = $collectionEndDate;
    }

    public function setApplicationStartDate(DateTime $applicationStartDate): void
    {
        $this->applicationStartDate = $applicationStartDate;
    }

    public function setApplicationEndDate(DateTime $applicationEndDate): void
    {
        $this->applicationEndDate = $applicationEndDate;
    }

    public function setPartnerGroup(UserGroup $partnerGroup): void
    {
        $this->partnerGroup = $partnerGroup;
    }

    public function setStudentGroup(UserGroup $studentGroup): void
    {
        $this->studentGroup = $studentGroup;
    }
}