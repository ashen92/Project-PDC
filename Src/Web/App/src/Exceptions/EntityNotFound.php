<?php
declare(strict_types=1);

namespace App\Exceptions;

class EntityNotFound extends \Exception
{
    public function __construct(
        string $message = 'Entity not found',
        int $code = 404,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}