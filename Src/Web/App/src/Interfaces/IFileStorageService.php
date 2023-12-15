<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IFileStorageService
{
    /**
     * Summary of upload
     * @param array $files Array of files
     * @return array JSON response as associative array
     */
    public function upload(array $files): array|bool;

    /**
     * Summary of get
     * @param string $filePath Path to file
     * @return array|bool Returns file as 'content' and 
     * its mime type as 'mimeType' in an associative array
     * or 'false' if file not found or on failure
     */
    public function get(string $filePath): array|bool;
}