<?php
declare(strict_types=1);

namespace App\Entities;

use App\DTOs\CreateRequirementDTO;
use App\Models\RequirementRepeatInterval;
use App\Models\RequirementType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "requirements")]
class Requirement
{
    // A requirement can be repeated up to 6 months after the start date.
    const MAXIMUM_REPEAT_DURATION = "P6M";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "requirement_type")]
    private RequirementType $requirementType;

    #[ORM\Column(type: "datetime")]
    private DateTime $startDate;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $endBeforeDate;

    #[ORM\Column(type: "requirement_repeat_interval", nullable: true)]
    private ?RequirementRepeatInterval $repeatInterval;

    #[ORM\Column]
    private string $fulfillMethod;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private ?array $allowedFileTypes;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxFileSize;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $maxFileCount;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: "requirements")]
    #[ORM\JoinColumn(name: "internship_cycle_id", referencedColumnName: "id")]
    private InternshipCycle $internshipCycle;

    #[ORM\OneToMany(targetEntity: UserRequirement::class, mappedBy: 'requirement')]
    private Collection $userRequirements;

    public function __construct(CreateRequirementDTO $requirementDTO)
    {
        $this->name = $requirementDTO->name;
        $this->description = $requirementDTO->description;
        $this->requirementType = RequirementType::fromString($requirementDTO->requirementType);
        $this->startDate = $requirementDTO->startDate;
        $this->endBeforeDate = $requirementDTO->endBeforeDate;

        if ($requirementDTO->repeatInterval) {
            $this->repeatInterval = RequirementRepeatInterval::fromString($requirementDTO->repeatInterval);
        } else {
            $this->repeatInterval = null;
        }

        $this->fulfillMethod = $requirementDTO->fulfillMethod;
        $this->allowedFileTypes = $requirementDTO->allowedFileTypes;
        $this->maxFileSize = $requirementDTO->maxFileSize;
        $this->maxFileCount = $requirementDTO->maxFileCount;
        $this->userRequirements = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRequirementType(): RequirementType
    {
        return $this->requirementType;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndBeforeDate(): ?DateTime
    {
        return $this->endBeforeDate;
    }

    public function getRepeatInterval(): ?RequirementRepeatInterval
    {
        return $this->repeatInterval;
    }

    public function getFulfillMethod(): string
    {
        return $this->fulfillMethod;
    }

    public function getAllowedFileTypes(): ?array
    {
        return $this->allowedFileTypes;
    }

    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }

    public function getMaxFileCount(): ?int
    {
        return $this->maxFileCount;
    }

    public function getUserRequirements(): Collection
    {
        return $this->userRequirements;
    }

    public function getInternshipCycle(): InternshipCycle
    {
        return $this->internshipCycle;
    }

    public function setInternshipCycle(InternshipCycle $internshipCycle): void
    {
        $this->internshipCycle = $internshipCycle;
    }
}