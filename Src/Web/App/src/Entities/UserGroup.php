<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_groups")]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $name;

    /**
     * Many Groups have Many Users.
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "groups")]
    #[ORM\JoinTable(name: "user_group_membership")]
    private Collection $users;

    /**
     * Many Groups have Many Roles.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: "groups")]
    private Collection $roles;

    public function __construct(string $name)
    {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addToGroup($this);
        }
    }

    public function addUsersFrom(UserGroup $group): void
    {
        foreach ($group->getUsers() as $user) {
            $this->addUser($user);
        }
    }

    public function addToRole(Role $role): void
    {
        $this->roles[] = $role;
    }
}