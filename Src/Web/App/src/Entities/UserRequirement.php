<?php
declare(strict_types=1);

namespace App\Entities;

use App\Models\Requirement\Type;
use DateTimeImmutable;
use DateTimeInterface;
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

    #[ORM\Column(type: "requirement_type")]
    private Type $requirementType;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $startDate;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $endDate;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column]
    private string $status;

    #[ORM\Column(nullable: true)]
    private ?string $originalFileName;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private ?array $filePaths;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $textResponse;

    public function __construct(User $user, Requirement $requirement)
    {
        $this->user = $user;
        $this->requirement = $requirement;
        $this->requirementType = $requirement->getRequirementType();
        $this->startDate = new DateTimeImmutable("now");
        $this->endDate = new DateTimeImmutable("+2 month");
        $this->completedAt = null;
        $this->status = "pending";
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function getFilePaths(): ?array
    {
        return $this->filePaths;
    }

    public function getTextResponse(): string
    {
        return $this->textResponse;
    }

    public function setStartDate(DateTimeInterface $startDate): void
    {
        $this->startDate = DateTimeImmutable::createFromInterface($startDate);
    }

    public function setEndDate(DateTimeInterface $endDate): void
    {
        $this->endDate = DateTimeImmutable::createFromInterface($endDate);
    }

    public function setCompletedAt(DateTimeInterface $completedAt): void
    {
        $this->completedAt = DateTimeImmutable::createFromInterface($completedAt);
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Summary of setFilePaths
     * @param array $filePaths Array of strings
     * @return void
     */
    public function setFilePaths(array $filePaths): void
    {
        $this->filePaths = $filePaths;
    }

    public function setTextResponse(string $textResponse): void
    {
        $this->textResponse = $textResponse;
    }
}