<?php
declare(strict_types=1);

namespace App\Models;

class InternshipSearchResult
{
    public function __construct(
        public readonly Internship $internship,
        public readonly string $organizationName,
        public readonly string $organizationLogoFilePath,
        private ?string $organizationLogo = null,
    ) {

    }

    public function setOrganizationLogo(string $base64Image): void
    {
        $this->organizationLogo = $base64Image;
    }

    public function getOrganizationLogo(): ?string
    {
        return $this->organizationLogo;
    }
}