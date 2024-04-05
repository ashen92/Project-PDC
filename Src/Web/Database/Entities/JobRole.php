<?php
declare(strict_types=1);

namespace Database\Entities;

use DB\Entities\InternshipCycle;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "job_roles")]
class JobRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: 'internships')]
    #[ORM\JoinColumn(name: 'internship_cycle_id', referencedColumnName: 'id')]
    private InternshipCycle $internshipCycle;

    public function __construct(
        string $name,
        InternshipCycle $internshipCycle
    ) {
        $this->name = $name;
        $this->internshipCycle = $internshipCycle;
    }
}