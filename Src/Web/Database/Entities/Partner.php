<?php
declare(strict_types=1);

namespace DB\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "partners")]
class Partner extends User
{
    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: "organization_id", referencedColumnName: "id")]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Partner::class, inversedBy: "manage")]
    private ?Partner $managedBy = null;

    #[ORM\OneToMany(targetEntity: Partner::class, mappedBy: "managedBy")]
    private Collection $manage;

    public function __construct(
        ?string $email = null,
        ?string $firstName = null,
        ?string $passwordHash = null
    ) {
        parent::__construct($email, $firstName, $passwordHash);
        $this->manage = new ArrayCollection();
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getManagedBy(): ?Partner
    {
        return $this->managedBy;
    }

    public function getManage(): Collection
    {
        return $this->manage;
    }

    public function addToManage(Partner $partner): void
    {
        $this->manage[] = $partner;
        $partner->setManagedBy($this);
    }

    public function setManagedBy(?Partner $managedBy): void
    {
        $this->managedBy = $managedBy;
    }

    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }
}