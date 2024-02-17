<?php
declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'internship_cycles')]
class InternshipCycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $endedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $jobCollectionStart;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $jobCollectionEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $applyingStart;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $applyingEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $interningStart;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $interningEnd;

    #[ORM\OneToOne(targetEntity: UserGroup::class)]
    #[ORM\JoinColumn(name: 'student_group_id', referencedColumnName: 'id')]
    private UserGroup $studentGroup;

    /**
     * @var Collection<int, UserGroup>
     */
    #[ORM\JoinTable(name: 'internship_cycle_partner_groups')]
    #[ORM\JoinColumn(name: 'internship_cycle_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'usergroup_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: 'UserGroup')]
    private Collection $partnerGroups;

    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'internshipCycle')]
    private Collection $internships;

    #[ORM\OneToMany(targetEntity: Requirement::class, mappedBy: 'internshipCycle')]
    private Collection $requirements;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->partnerGroups = new ArrayCollection();
        $this->internships = new ArrayCollection();
        $this->requirements = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getJobCollectionStart(): ?DateTimeImmutable
    {
        return $this->jobCollectionStart;
    }

    public function getJobCollectionEnd(): ?DateTimeImmutable
    {
        return $this->jobCollectionEnd;
    }

    public function getApplyingStart(): ?DateTimeImmutable
    {
        return $this->applyingStart;
    }

    public function getApplyingEnd(): ?DateTimeImmutable
    {
        return $this->applyingEnd;
    }

    public function getInterningStart(): ?DateTimeImmutable
    {
        return $this->interningStart;
    }

    public function getInterningEnd(): ?DateTimeImmutable
    {
        return $this->interningEnd;
    }

    public function getStudentGroup(): UserGroup
    {
        return $this->studentGroup;
    }

    public function getStudentUserGroupName(): string
    {
        return $this->studentGroup->getName();
    }

    public function setEndedAt(DateTimeImmutable $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function setJobCollectionStart(DateTimeImmutable $jobCollectionStart): void
    {
        $this->jobCollectionStart = $jobCollectionStart;
    }

    public function setJobCollectionEnd(DateTimeImmutable $jobCollectionEnd): void
    {
        $this->jobCollectionEnd = $jobCollectionEnd;
    }

    public function setApplyingStart(DateTimeImmutable $applyingStart): void
    {
        $this->applyingStart = $applyingStart;
    }

    public function setApplyingEnd(DateTimeImmutable $applyingEnd): void
    {
        $this->applyingEnd = $applyingEnd;
    }

    public function setInterningStart(DateTimeImmutable $interningStart): void
    {
        $this->interningStart = $interningStart;
    }

    public function setInterningEnd(DateTimeImmutable $interningEnd): void
    {
        $this->interningEnd = $interningEnd;
    }

    public function addPartnerGroup(UserGroup $partnerGroup): void
    {
        $this->partnerGroups[] = $partnerGroup;
    }

    public function setStudentGroup(UserGroup $studentGroup): void
    {
        $this->studentGroup = $studentGroup;
    }
}