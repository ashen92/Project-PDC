<?php
declare(strict_types=1);

namespace App\Entities\Permission;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'permission_actions')]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}