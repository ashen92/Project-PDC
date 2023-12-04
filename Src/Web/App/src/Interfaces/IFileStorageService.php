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
}