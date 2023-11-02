<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "jobroles")]
class JobRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: 'jobRoles')]
    #[ORM\JoinColumn(name: 'internshipcycle_id', referencedColumnName: 'id')]
    private InternshipCycle $internshipCycle;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}