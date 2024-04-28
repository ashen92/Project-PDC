<?php
declare(strict_types=1);

namespace App\Models\InternshipProgram;

readonly class CreateApplication
{
    /**
     * @param array<\Symfony\Component\HttpFoundation\File\UploadedFile> $files
     */
    public function __construct(
        public int $internshipId,
        public int $userId,
        public array $files,
    ) {

    }
}