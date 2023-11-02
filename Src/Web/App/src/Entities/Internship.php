<?php
declare(strict_types=1);

namespace App\Entities;

use App\Repositories\InternshipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InternshipRepository::class)]
#[ORM\Table(name: "internships")]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'internshipsCreated')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: InternshipCycle::class, inversedBy: 'internships')]
    #[ORM\JoinColumn(name: 'internshipcycle_id', referencedColumnName: 'id')]
    private InternshipCycle $internshipCycle;

    public function __construct(string $title, string $description, User $user, InternshipCycle $internshipCycle)
    {
        $this->title = $title;
        $this->description = $description;
        $this->user = $user;
        $user->addToInternshipsCreated($this);
        $this->internshipCycle = $internshipCycle;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}