<?php
declare(strict_types=1);

namespace App\Models;

class SignupEmail extends Email
{
    public function __construct(
        string $receiverAddress,
        string $receiverName,
        string $token
    ) {
        $subject = "Welcome to Professional Development Center at UCSC";
        $bodyPlainText = <<<EOT
            Click the link below to create your account.
            http://localhost:80/signup/continue?token=$token
            EOT;

        parent::__construct($receiverAddress, $receiverName, $subject, $bodyPlainText);
    }
}