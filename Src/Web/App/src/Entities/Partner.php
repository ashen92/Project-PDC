<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "partners")]
class Partner extends User
{
    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: "owner")]
    private Collection $internshipsCreated;

    public function addToInternshipsCreated(Internship $internship): void
    {
        $this->internshipsCreated[] = $internship;
    }

    public function __construct($email, $firstName, $passwordHash)
    {
        parent::__construct($email, $firstName, $passwordHash);
        $this->internshipsCreated = new ArrayCollection();
    }
}