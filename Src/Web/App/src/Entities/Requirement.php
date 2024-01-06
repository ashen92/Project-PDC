<?php
declare(strict_types=1);

namespace App\Entities;

use App\DTOs\CreateRequirementDTO;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "requirements")]
class Requirement
{
    // A requirement can be repeated up to 6 months after the start date.
    const string MAXIMUM_REPEAT_DURATION = "P6M";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "requirement_type")]
    private Type $requirementType;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $startDate;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $endBeforeDate;

    #[ORM\Column(type: "requirement_repeat_interval", nullable: true)]
    private ?RepeatInterval $repeatInterval;

    #[ORM\Column(type: "requirement_fulfill_method")]
    private FulFillMethod $fulfillMethod;

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

    public function __construct(CreateRequirementDTO $dto)
    {
        $this->name = $dto->name;
        $this->description = $dto->description;
        $this->requirementType = Type::fromString($dto->requirementType);
        $this->startDate = $dto->startDate;

        if ($this->requirementType === Type::ONE_TIME) {
            $this->endBeforeDate = $dto->endBeforeDate;
            $this->repeatInterval = null;
        } else {
            $this->repeatInterval = RepeatInterval::fromString($dto->repeatInterval);
            $this->endBeforeDate = null;
        }

        $this->fulfillMethod = FulFillMethod::fromString($dto->fulfillMethod);

        if ($this->fulfillMethod === FulFillMethod::FILE_UPLOAD) {
            $this->allowedFileTypes = $dto->allowedFileTypes;
            $this->maxFileSize = $dto->maxFileSize;
            $this->maxFileCount = $dto->maxFileCount;
        } else {
            $this->allowedFileTypes = null;
            $this->maxFileSize = null;
            $this->maxFileCount = null;
        }
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

    public function getRequirementType(): Type
    {
        return $this->requirementType;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndBeforeDate(): ?DateTimeImmutable
    {
        return $this->endBeforeDate;
    }

    public function getRepeatInterval(): ?RepeatInterval
    {
        return $this->repeatInterval;
    }

    public function getFulfillMethod(): FulFillMethod
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