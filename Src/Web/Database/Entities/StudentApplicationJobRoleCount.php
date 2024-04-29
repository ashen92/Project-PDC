<?php
declare(strict_types=1);

namespace DB\Entities;

use DB\Entities\InternshipCycle;
use DB\Entities\Student;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "student_application_job_role_counts")]
class StudentApplicationJobRoleCount
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: InternshipCycle::class)]
    #[ORM\JoinColumn(name: 'internship_cycle_id', referencedColumnName: 'id')]
    private InternshipCycle $internshipCycle;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\Column(type: 'integer')]
    private int $ApplicationCount;

    #[ORM\Column(type: 'integer')]
    private int $jobRoleCount;

    public function __construct(
        InternshipCycle $internshipCycle,
        Student $student,
        int $ApplicationCount,
        int $jobRoleCount
    ) {
        $this->internshipCycle = $internshipCycle;
        $this->student = $student;
        $this->ApplicationCount = $ApplicationCount;
        $this->jobRoleCount = $jobRoleCount;
    }
}