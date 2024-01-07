<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IFileStorageService
{
    /**
     * @param array<\Symfony\Component\HttpFoundation\File\UploadedFile> $files Array of files
     * @return array<mixed>|bool JSON response as associative array
     */
    public function upload(array $files): array|bool;

    /**
     * @return array<string, string>|bool Returns file as 'content' and 
     * its mime type as 'mimeType' in an associative array
     * or 'false' if file not found or on failure
     */
    public function get(string $filePath): array|bool;
}