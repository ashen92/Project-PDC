<?php
declare(strict_types=1);

namespace DB\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'permissions')]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column]
    private string $name;

    /**
     * Many Permissions have Many Roles.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->roles = new ArrayCollection();
    }

    public function addToRole(Role $role): void
    {
        $this->roles[] = $role;
    }
}