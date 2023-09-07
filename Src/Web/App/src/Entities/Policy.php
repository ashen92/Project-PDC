<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'policies')]
class Policy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private $name;

    /**
     * Many Policies have Many Roles.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'policies')]
    private Collection $roles;

    /**
     * @param $name Should start with "Can", then the action. Eg. CanRemoveUser
     */
    public function __construct(string $name)
    {
        $this->roles = new ArrayCollection();
        $this->name = $name;
    }

    public function addToRole(Role $role): void
    {
        $this->roles[] = $role;
    }
}