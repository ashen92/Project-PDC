<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "organizations")]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $name;

    #[ORM\Column(type: "string")]
    private string $address;

    #[ORM\Column(type: "string")]
    private string $city;

    #[ORM\Column(type: "string")]
    private string $industry;

    #[ORM\Column(type: "string")]
    private string $website;

    #[ORM\Column(type: "string")]
    private string $tagline;

    #[ORM\Column]
    private string $logoFilePath;

    // constructor

    public function __construct(
        string $name,
        string $address,
        string $city,
        string $industry,
        string $website,
        string $tagline,
        string $logoFilePath
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->industry = $industry;
        $this->website = $website;
        $this->tagline = $tagline;
        $this->logoFilePath = $logoFilePath;
    }

    // getters

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

    // setters

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setIndustry(string $industry): void
    {
        $this->industry = $industry;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function setTagline(string $tagline): void
    {
        $this->tagline = $tagline;
    }

    public function setLogoFilePath(string $logoFilePath): void
    {
        $this->logoFilePath = $logoFilePath;
    }
}