<?php
declare(strict_types=1);

namespace App\Entities;

use App\Models\Requirement\FulFillMethod;
use App\Models\UserRequirement\Status;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_requirements")]
class UserRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: "assignedRequirements")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Requirement::class, inversedBy: "userRequirements")]
    #[ORM\JoinColumn(name: "requirement_id", referencedColumnName: "id")]
    private Requirement $requirement;

    #[ORM\Column(type: "requirement_fulfill_method")]
    private FulFillMethod $fulfillMethod;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $startDate;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $endDate;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column(type: 'user_requirement_status')]
    private Status $status;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private ?array $filePaths;

    /**
     * @var Collection<int, File>
     */
    #[ORM\JoinTable(name: 'user_requirement_files')]
    #[ORM\JoinColumn(name: 'user_requirement_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'file_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: 'File')]
    private Collection $files;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $textResponse;

    public function __construct(
        User $user,
        Requirement $requirement,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ) {
        $this->user = $user;
        $this->requirement = $requirement;
        $this->fulfillMethod = $requirement->getFulfillMethod();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->completedAt = null;
        $this->status = Status::PENDING;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRequirement(): Requirement
    {
        return $this->requirement;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getFilePaths(): ?array
    {
        return $this->filePaths;
    }

    public function getTextResponse(): string
    {
        return $this->textResponse;
    }

    public function setStartDate(DateTimeInterface $date): void
    {
        $this->startDate = DateTimeImmutable::createFromInterface($date);
    }

    public function setEndDate(DateTimeInterface $date): void
    {
        $this->endDate = DateTimeImmutable::createFromInterface($date);
    }

    public function setCompletedAt(DateTimeInterface $date): void
    {
        $this->completedAt = DateTimeImmutable::createFromInterface($date);
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function setFilePaths(array $filePaths): void
    {
        $this->filePaths = $filePaths;
    }

    public function setTextResponse(string $textResponse): void
    {
        $this->textResponse = $textResponse;
    }
}