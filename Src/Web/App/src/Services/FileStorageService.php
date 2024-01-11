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
        private HttpClientInterface $httpClient,
        private string $fileStorageAPIEndpoint
    ) {
    }

    public function upload(array $files): ?array
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
                $this->fileStorageAPIEndpoint,
                [
                    "headers" => $headers,
                    "body" => $body
                ]
            );

            if ($response->getStatusCode() === 200) {
                return $response->toArray()["files"];
            } else {
                return null;
            }

        } catch (\Throwable $th) {
            return null;
        }
    }

    public function get(string $filePath): array|bool
    {
        // Temporary solution
        return false;

        try {
            $response = $this->httpClient->request(
                "GET",
                $this->fileStorageAPIEndpoint . "/" . $filePath
            );

            $return = [];

            if ($response->getStatusCode() === 200) {
                $return["content"] = $response->getContent();
                $return["mimeType"] = $response->getHeaders()["content-type"][0];
                return $return;
            } else {
                return false;
            }

        } catch (\Throwable $th) {
            return false;
        }
    }
}