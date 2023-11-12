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

    #[ORM\ManyToOne(targetEntity: Requirement::class, inversedBy: "userRequirements")]
    #[ORM\JoinColumn(name: "requirement_id", referencedColumnName: "id")]
    private Requirement $requirement;

    #[ORM\Column(type: "datetime")]
    private DateTime $startDate;

    #[ORM\Column(type: "datetime")]
    private DateTime $endDate;

    #[ORM\Column(type: "datetime")]
    private DateTime|null $completedAt;

    #[ORM\Column]
    private string $status;

    public function __construct()
    {

    }
}