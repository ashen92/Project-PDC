<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IFileStorageService
{
    /**
     * @param array<\Symfony\Component\HttpFoundation\File\UploadedFile> $files Array of files
     * @return array<array<string, string>>|null Foreach file uploaded returns its name and path in an associative array.
     * Example:
     * [
     *     [
     *         "name" => "example.jpg",
     *         "path" => "path/to/example.jpg"
     *     ],
     *     ...
     * ]
     * Returns null if the upload fails.
     */
    public function upload(array $files): ?array;

    /**
     * @return array<string, string>|bool Returns file as 'content' and 
     * its mime type as 'mimeType' in an associative array
     * or 'false' if file not found or on failure
     */
    public function get(string $filePath): array|bool;
}