<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    /**
     * Many Roles have Many Groups.
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'roles_groups')]
    private Collection $groups;

    /**
     * Many Roles have Many Policies.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Policy::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'roles_policies')]
    private Collection $policies;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->policies = new ArrayCollection();
    }

    public function addGroup(Group $group): void
    {
        if(!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addToRole($this);
        }
    }

    public function addPolicy(Policy $policy): void
    {
        if(!$this->policies->contains($policy)) {
            $this->policies[] = $policy;
            $policy->addToRole($this);
        }
    }
}