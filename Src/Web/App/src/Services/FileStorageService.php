<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IFileStorageService;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FileStorageService implements IFileStorageService
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function upload(array $files): array|bool
    {
        $formData = [];
        foreach ($files as $file) {
            $dataPart = DataPart::fromPath($file->getPathname(), md5($file->getClientOriginalName()), $file->getMimeType());
            $formData[] = $dataPart;
        }

        $formDataPart = new FormDataPart($formData);
        $body = $formDataPart->bodyToIterable();
        $headers = $formDataPart->getPreparedHeaders()->toArray();

        try {
            $response = $this->httpClient->request(
                "POST",
                "http://localhost:3000/upload",
                [
                    "headers" => $headers,
                    "body" => $body
                ]
            );

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getContent(), true);
            } else {
                return false;
            }

        } catch (\Throwable $th) {
            return false;
        }
    }
}