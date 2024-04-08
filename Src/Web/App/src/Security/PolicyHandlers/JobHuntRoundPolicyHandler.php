<?php
declare(strict_types=1);

namespace App\Security\PolicyHandlers;

use App\Security\IPolicyHandler;
use App\Security\Policies\JobHuntRoundPolicy;

class JobHuntRoundPolicyHandler implements IPolicyHandler
{
    public function __construct()
    {
    }

    /**
     * @param JobHuntRoundPolicy $policy
     */
    function handle(int $userId, $policy): bool
    {
        throw new \Exception('Not implemented');
    }
}