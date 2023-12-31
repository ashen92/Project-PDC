<?php
declare(strict_types=1);

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "user" => User::class,
    "partner" => Partner::class,
    "student" => Student::class
])]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(nullable: true)]
    private ?string $email;

    #[ORM\Column(nullable: true)]
    private ?string $firstName;

    #[ORM\Column(nullable: true)]
    private ?string $lastName;

    #[ORM\Column(nullable: true)]
    private ?string $passwordHash;

    #[ORM\Column(type: "boolean")]
    private bool $isActive = false;

    #[ORM\Column(nullable: true)]
    private ?string $activationToken;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $activationTokenExpiresAt;

    /**
     * Many Users have Many Groups.
     * @var Collection<int, UserGroup>
     */
    #[ORM\ManyToMany(targetEntity: UserGroup::class, mappedBy: "users")]
    private Collection $groups;

    public function __construct(
        ?string $email = null,
        ?string $firstName = null,
        ?string $passwordHash = null
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->passwordHash = $passwordHash;
        $this->groups = new ArrayCollection();
    }

    public function generateActivationToken(): string
    {
        $this->activationToken = bin2hex(random_bytes(32));
        $this->activationTokenExpiresAt = new DateTime("+1 day");
        return $this->activationToken;
    }

    public function resetActivationToken(): void
    {
        $this->activationToken = null;
        $this->activationTokenExpiresAt = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function getActivationTokenExpiresAt(): ?DateTime
    {
        return $this->activationTokenExpiresAt;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setActivationToken(?string $activationToken): void
    {
        $this->activationToken = $activationToken;
    }

    public function setActivationTokenExpiresAt(?DateTime $activationTokenExpiresAt): void
    {
        $this->activationTokenExpiresAt = $activationTokenExpiresAt;
    }

    public function addToGroup(UserGroup $group): void
    {
        $this->groups[] = $group;
    }
}