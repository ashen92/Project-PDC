<?php
declare(strict_types=1);

namespace DB\Entities;

use App\Models\Application\Status;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'applications')]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Internship::class)]
    #[ORM\JoinColumn(name: 'internship_id', referencedColumnName: 'id')]
    private ?Internship $internship;

    #[ORM\ManyToOne(targetEntity: JobRole::class)]
    #[ORM\JoinColumn(name: 'jobRoleId', referencedColumnName: 'id')]
    private ?JobRole $jobRole;

    #[ORM\Column(type: 'application_status')]
    private Status $status;

    public function __construct(
        Student $student,
        ?Internship $internship,
        ?JobRole $jobRole,
        Status $status
    ) {
        $this->student = $student;
        $this->internship = $internship;
        $this->jobRole = $jobRole;
        $this->status = $status;
    }
}