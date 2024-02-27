<?php
declare(strict_types=1);

namespace App\Models;

class Partner extends User implements \JsonSerializable
{
    public function __construct(
        private ?int $organization_id,
        private ?int $managedBy_partner_id,
        int $id,
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?string $passwordHash,
        bool $isActive,
        ?string $activationToken,
        ?\DateTimeImmutable $activationTokenExpiresAt,
        string $type,
    ) {
        parent::__construct(
            $id,
            $email,
            $firstName,
            $lastName,
            $passwordHash,
            $isActive,
            $activationToken,
            $activationTokenExpiresAt,
            $type,
        );
    }

    public function getOrganizationId(): int
    {
        return $this->organization_id;
    }

    public function getManagedByPartnerId(): ?int
    {
        return $this->managedBy_partner_id;
    }

    public function setOrganizationId(int $organization_id): void
    {
        $this->organization_id = $organization_id;
    }

    public function setManagedByPartnerId(?int $managedBy_partner_id): void
    {
        $this->managedBy_partner_id = $managedBy_partner_id;
    }

    /**
     * Specify data which should be serialized to JSON
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource .
     */
    function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), get_object_vars($this));
    }
}