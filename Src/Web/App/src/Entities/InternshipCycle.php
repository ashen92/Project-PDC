<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "internshipcycles")]
class InternshipCycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'internshipCycle')]
    private Collection $internhips;

    #[ORM\OneToMany(targetEntity: JobRole::class, mappedBy: 'internshipCycle')]
    private Collection $jobRoles;

    public function __construct()
    {
        $this->internhips = new ArrayCollection();
        $this->jobRoles = new ArrayCollection();
    }
}