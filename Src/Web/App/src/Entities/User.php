<?php
declare(strict_types=1);

namespace App\Entities;

use App\Repositories\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $email;

    #[ORM\Column]
    private string $firstName;

    /**
     * Many Users have Many Groups.
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: "users")]
    private Collection $groups;

    public function __construct(string $email, string $firstName)
    {
        $this->groups = new ArrayCollection();
        $this->email = $email;
        $this->firstName = $firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function addToGroup(Group $group): void
    {
        $this->groups[] = $group;
    }
}