<?php
declare(strict_types=1);

namespace App\Interfaces;

interface IUserGroupService
{
    public function getUserGroupsForInternshipProgram(): array;
}