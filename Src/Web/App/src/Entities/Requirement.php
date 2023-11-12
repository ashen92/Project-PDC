<?php
declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "requirements")]
class Requirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $description;

    #[ORM\Column]
    private string $type;

    #[ORM\Column(type: "datetime")]
    private DateTime $startDate;

    #[ORM\Column(type: "datetime", nullable: true)]
    private DateTime|null $endBeforeDate;

    #[ORM\Column(nullable: true)]
    private string|null $repeatInterval;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: "requirements")]
    #[ORM\JoinColumn(name: "internship_cycle_id", referencedColumnName: "id")]
    private InternshipCycle $internshipCycle;

    #[ORM\OneToMany(targetEntity: UserRequirement::class, mappedBy: 'requirement')]
    private Collection $userRequirements;

    public function __construct(string $name)
    {
        $this->scheduledRequirements = new ArrayCollection();
        $this->name = $name;
        $this->description = "";
        $this->type = "";
        $this->startDate = new DateTime("now");
        $this->endBeforeDate = new DateTime("now");
    }
}