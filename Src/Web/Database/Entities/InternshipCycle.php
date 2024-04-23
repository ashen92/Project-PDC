<?php
declare(strict_types=1);

namespace DB\Entities;

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
    private ?DateTimeImmutable $jobHuntRound1Start;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $jobHuntRound1End;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $jobHuntRound2Start;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $jobHuntRound2End;

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

    public function getJobHuntRound1Start(): ?DateTimeImmutable
    {
        return $this->jobHuntRound1Start;
    }

    public function getJobHuntRound1End(): ?DateTimeImmutable
    {
        return $this->jobHuntRound1End;
    }

    public function getJobHuntRound2Start(): ?DateTimeImmutable
    {
        return $this->jobHuntRound2Start;
    }

    public function getJobHuntRound2End(): ?DateTimeImmutable
    {
        return $this->jobHuntRound2End;
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

    public function setJobHuntRound1Start(DateTimeImmutable $jobHuntRound1Start): void
    {
        $this->jobHuntRound1Start = $jobHuntRound1Start;
    }

    public function setJobHuntRound1End(DateTimeImmutable $jobHuntRound1End): void
    {
        $this->jobHuntRound1End = $jobHuntRound1End;
    }

    public function setJobHuntRound2Start(DateTimeImmutable $jobHuntRound2Start): void
    {
        $this->jobHuntRound2Start = $jobHuntRound2Start;
    }

    public function setJobHuntRound2End(DateTimeImmutable $jobHuntRound2End): void
    {
        $this->jobHuntRound2End = $jobHuntRound2End;
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