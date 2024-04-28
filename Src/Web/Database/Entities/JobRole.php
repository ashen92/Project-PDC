<?php
declare(strict_types=1);

namespace DB\Entities;

use DB\Entities\InternshipCycle;
use DB\Entities\Student;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'jobRoles')]
    #[ORM\JoinTable(name: 'job_role_students')]
    private Collection $students;

    public function __construct(
        string $name,
        InternshipCycle $internshipCycle
    ) {
        $this->name = $name;
        $this->internshipCycle = $internshipCycle;
    }
}