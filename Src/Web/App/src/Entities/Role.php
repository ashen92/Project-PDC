<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class Role
{
    /**
     * Many Roles have Many Groups.
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'roles')]
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }
}