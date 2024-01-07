<?php
declare(strict_types=1);

namespace App\DTOs;

class UserRequirementCompletionDTO
{
    /**
     * @param array<\Symfony\Component\HttpFoundation\File\UploadedFile>|null $files
     */
    public function __construct(
        public int $userRequirementId,
        public ?array $files,
        public ?string $textResponse,
    ) {
    }
}