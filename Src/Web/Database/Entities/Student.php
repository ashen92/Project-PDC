<?php
declare(strict_types=1);

namespace DB\Entities;

use DB\Entities\JobRole;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "students")]
class Student extends User
{
    #[ORM\Column]
    private string $studentEmail;

    #[ORM\Column]
    private string $fullName;

    #[ORM\Column]
    private string $registrationNumber;

    #[ORM\Column]
    private string $indexNumber;

    #[ORM\OneToMany(targetEntity: UserRequirement::class, mappedBy: 'user')]
    private Collection $assignedRequirements;

    #[ORM\ManyToMany(targetEntity: JobRole::class, mappedBy: 'students')]
    private Collection $jobRoles;

    public function __construct(
        string $studentEmail,
        string $fullName,
        string $registrationNumber,
        string $indexNumber,
        ?string $email = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $passwordHash = null,
    ) {
        parent::__construct($email, $firstName, $lastName, $passwordHash);
        $this->studentEmail = $studentEmail;
        $this->fullName = $fullName;
        $this->registrationNumber = $registrationNumber;
        $this->indexNumber = $indexNumber;
    }

    public function getStudentEmail(): string
    {
        return $this->studentEmail;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getIndexNumber(): string
    {
        return $this->indexNumber;
    }

    public function setStudentEmail(string $studentEmail): void
    {
        $this->studentEmail = $studentEmail;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function setIndexNumber(string $indexNumber): void
    {
        $this->indexNumber = $indexNumber;
    }
}