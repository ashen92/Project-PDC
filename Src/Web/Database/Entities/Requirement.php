<?php
declare(strict_types=1);

namespace DB\Entities;

use App\DTOs\CreateRequirementDTO;
use App\Models\Requirement\FulFillMethod;
use App\Models\Requirement\RepeatInterval;
use App\Models\Requirement\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "requirements")]
class Requirement
{
    // A requirement can be repeated up to 6 months after the start date.
    public const MAXIMUM_REPEAT_DURATION = "P6M";
    private const MAXIMUM_WEEKS = 24;

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

    #[ORM\Column(type: "string")]
    private string $startWeek;

    #[ORM\Column(type: "string")]
    private string $durationWeeks;

    #[ORM\Column(type: "requirement_repeat_interval", nullable: true)]
    private ?RepeatInterval $repeatInterval;

    #[ORM\Column(type: "requirement_fulfill_method")]
    private FulFillMethod $fulfillMethod;

    #[ORM\Column(nullable: true)]
    private ?string $allowedFileTypes;

    #[ORM\Column(nullable: true)]
    private ?int $maxFileSize;

    #[ORM\Column(nullable: true)]
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
        $this->requirementType = $dto->requirementType;
        $this->startWeek = $dto->startWeek->format('%d days');
        $this->durationWeeks = $dto->durationWeeks->format('%d days');

        if ($this->requirementType === Type::ONE_TIME) {
            $this->repeatInterval = null;
        } else {
            $this->repeatInterval = $dto->repeatInterval;
        }

        $this->fulfillMethod = $dto->fulfillMethod;

        if ($this->fulfillMethod === FulFillMethod::FILE_UPLOAD) {
            $this->allowedFileTypes = json_encode($dto->allowedFileTypes);
            $this->maxFileSize = $dto->maxFileSize;
            $this->maxFileCount = $dto->maxFileCount;
        } else {
            $this->allowedFileTypes = null;
            $this->maxFileSize = null;
            $this->maxFileCount = null;
        }
        $this->userRequirements = new ArrayCollection();
    }

    // public function createUserRequirements(User $user, EntityManager $em): void
    // {
    //     if ($this->requirementType === Type::ONE_TIME) {

    //         $ur = new UserRequirement($user, $this, $this->startDate, $this->endBeforeDate);
    //         $this->userRequirements->add($ur);
    //         $em->persist($ur);

    //         return;
    //     }

    //     $interval = new DateInterval($this->repeatInterval->toDuration());

    //     $startDate = $this->startDate;
    //     $currentDate = $this->startDate;

    //     while ($currentDate < $startDate->add(new DateInterval(self::MAXIMUM_REPEAT_DURATION))) {
    //         $ur = new UserRequirement($user, $this, $currentDate, $currentDate->add($interval));
    //         $this->userRequirements->add($ur);
    //         $currentDate = $currentDate->add($interval);
    //         $em->persist($ur);
    //     }
    // }

    public function getFulfillMethod(): FulFillMethod
    {
        return $this->fulfillMethod;
    }

    public function setInternshipCycle(InternshipCycle $internshipCycle): void
    {
        $this->internshipCycle = $internshipCycle;
    }
}