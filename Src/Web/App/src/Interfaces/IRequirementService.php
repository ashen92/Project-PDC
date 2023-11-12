<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IRequirementService
{
    public function getRequirements(): array;
    public function addRequirement(string $name): void;
}