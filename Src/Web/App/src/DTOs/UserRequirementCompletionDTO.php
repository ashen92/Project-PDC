<?php
declare(strict_types=1);

namespace App\DTOs;

class UserRequirementCompletionDTO
{
    public function __construct(
        public int $requirementId,
        public array $files
    ) {
    }
}