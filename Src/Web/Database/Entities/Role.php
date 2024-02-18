<?php
declare(strict_types=1);

namespace DB\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "roles")]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    /**
     * Many Roles have Many Groups.
     * @var Collection<int, UserGroup>
     */
    #[ORM\ManyToMany(targetEntity: UserGroup::class, inversedBy: "roles")]
    #[ORM\JoinTable(name: "user_group_roles")]
    private Collection $groups;

    /**
     * Many Roles have Many Permissions.
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: "roles")]
    #[ORM\JoinTable(name: "role_permissions")]
    private Collection $permissions;

    public function __construct(string $name)
    {
        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->name = $name;
    }

    public function addGroup(UserGroup $group): void
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addToRole($this);
        }
    }

    public function addPermission(Permission $policy): void
    {
        if (!$this->permissions->contains($policy)) {
            $this->permissions[] = $policy;
            $policy->addToRole($this);
        }
    }

    public function removeGroup(UserGroup $group): void
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeFromRole($this);
        }
    }
}