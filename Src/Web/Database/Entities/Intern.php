<?php
declare(strict_types=1);

namespace DB\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interns')]
class Intern
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'adder_user_id', referencedColumnName: 'id')]
    private Student $adderUserId;

    #[ORM\OneToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id')]
    private Organization $organization;

    #[ORM\OneToOne(targetEntity: Application::class)]
    #[ORM\JoinColumn(name: 'application_id', referencedColumnName: 'id')]
    private ?Application $application = null;

    public function __construct(
        Student $student,
        User $adderUserId,
        Organization $organization,
        ?Application $application = null
    ) {
        $this->student = $student;
        $this->adderUserId = $adderUserId;
        $this->organization = $organization;
        $this->application = $application;
    }
}