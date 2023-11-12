<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "internship_cycles")]
class InternshipCycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\OneToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: "student_group_id", referencedColumnName: "id")]
    private Group $studentGroup;

    #[ORM\OneToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: "partner_group_id", referencedColumnName: "id")]
    private Group $partnerGroup;

    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'internshipCycle')]
    private Collection $internhips;

    #[ORM\OneToMany(targetEntity: Requirement::class, mappedBy: 'internshipCycle')]
    private Collection $requirements;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->internhips = new ArrayCollection();
        $this->requirements = new ArrayCollection();
    }
}