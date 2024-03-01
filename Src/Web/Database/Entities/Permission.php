<?php
declare(strict_types=1);

namespace DB\Entities;

use DB\Entities\Permission\Action;
use DB\Entities\Permission\Resource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
#[ORM\Table(name: 'permissions')]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ManyToOne(targetEntity: Resource::class)]
    #[JoinColumn(name: 'resource_id', referencedColumnName: 'id')]
    private Resource $resource;

    #[ManyToOne(targetEntity: Action::class)]
    #[JoinColumn(name: 'action_id', referencedColumnName: 'id')]
    private Action $action;

    /**
     * Many Permissions have Many Roles.
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
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