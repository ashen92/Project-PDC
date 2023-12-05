<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IEmailService;
use App\Models\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailAPI implements IEmailService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $emailAPIEndpoint
    ) {
    }

    public function sendEmail(Email $email): bool
    {
        try {
            $response = $this->httpClient->request(
                "POST",
                $this->emailAPIEndpoint,
                [
                    "headers" => [
                        "Content-Type" => "application/json",
                        "x-api-key" => "1234567890",
                    ],
                    "body" => json_encode($email),
                ]
            );

            return $response->getStatusCode() === 201;
        } catch (\Throwable $th) {
            return false;
        }
    }
}