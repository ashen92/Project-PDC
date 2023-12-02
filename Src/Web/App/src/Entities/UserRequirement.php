<?php
declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_requirements")]
class UserRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "assignedRequirements")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Requirement::class, inversedBy: "userRequirements")]
    #[ORM\JoinColumn(name: "requirement_id", referencedColumnName: "id")]
    private Requirement $requirement;

    #[ORM\Column(type: "datetime")]
    private DateTime $startDate;

    #[ORM\Column(type: "datetime")]
    private DateTime $endDate;

    #[ORM\Column(type: "datetime", nullable: true)]
    private DateTime|null $completedAt;

    #[ORM\Column]
    private string $status;

    public function __construct(User $user, Requirement $requirement)
    {
        $this->user = $user;
        $this->requirement = $requirement;
        $this->startDate = new DateTime("now");
        $this->endDate = new DateTime("+2 month");
        $this->completedAt = null;
        $this->status = "pending";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getCompletedAt(): ?DateTime
    {
        return $this->completedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}