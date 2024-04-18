<?php
declare(strict_types=1);

namespace Database\Entities;

use DB\Entities\UserRequirement;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_requirement_files')]
class UserRequirementFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserRequirement::class)]
    #[ORM\JoinColumn(name: 'user_requirement_id', referencedColumnName: 'id')]
    private UserRequirement $userRequirement;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $path;

    public function __construct(
        UserRequirement $application,
        string $name,
        string $path
    ) {
        $this->application = $application;
        $this->name = $name;
        $this->path = $path;
    }
}