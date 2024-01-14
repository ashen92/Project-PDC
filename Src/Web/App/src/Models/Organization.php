<?php
declare(strict_types=1);

namespace App\Models;

class Organization
{
    public function __construct(
        private int $id,
        private string $name,
        private string $address,
        private string $city,
        private string $industry,
        private string $website,
        private string $tagline,
        private string $logoFilePath,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getIndustry(): string
    {
        return $this->industry;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function getTagline(): string
    {
        return $this->tagline;
    }

    public function getLogoFilePath(): string
    {
        return $this->logoFilePath;
    }
}