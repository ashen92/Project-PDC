<?php
declare(strict_types=1);

namespace App\Entities;

use App\Security\Permission\Action;
use App\Security\Permission\Resource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "permissions")]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "permission_resource")]
    private Resource $resource;

    #[ORM\Column(type: "permission_action")]
    private Action $action;

    /**
     * Many Permissions have Many Roles.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: "permissions")]
    private Collection $roles;

    public function __construct(Resource $resource, Action $action)
    {
        $this->resource = $resource;
        $this->action = $action;
        $this->roles = new ArrayCollection();
    }

    public function addToRole(Role $role): void
    {
        $this->roles[] = $role;
    }
}