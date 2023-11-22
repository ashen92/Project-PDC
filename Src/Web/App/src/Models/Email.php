<?php
declare(strict_types=1);

namespace App\Models;

abstract class Email
{
    public function __construct(
        public string $receiverAddress,
        public string $receiverName,
        public string $subject,
        public string $bodyPlainText
    ) {
    }
}