<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IAuthorizationService
{
    public function getUserRoles(): array;
}