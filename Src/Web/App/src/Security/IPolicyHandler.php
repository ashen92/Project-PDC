<?php
declare(strict_types=1);

namespace App\Security;

interface IPolicyHandler
{
    /**
     * @param IPolicy $policy
     */
    public function handle(int $userId, $policy): bool;
}