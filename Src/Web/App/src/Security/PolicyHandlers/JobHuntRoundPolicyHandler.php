<?php
declare(strict_types=1);

namespace App\Security\PolicyHandlers;

use App\Repositories\InternshipProgramRepository;
use App\Security\IPolicyHandler;
use App\Security\Policies\JobHuntRoundPolicy;

readonly class JobHuntRoundPolicyHandler implements IPolicyHandler
{
    public function __construct(
        private InternshipProgramRepository $internshipProgramRepo,
    ) {
    }

    /**
     * @param JobHuntRoundPolicy $policy
     */
    function handle(int $userId, $policy): bool
    {
        $cycle = $this->internshipProgramRepo->findLatestCycle();
        if ($policy->round === JobHuntRoundPolicy::FirstRound && $cycle->isFirstRound()) {
            return true;
        }
        if ($policy->round === JobHuntRoundPolicy::SecondRound && $cycle->isSecondRound()) {
            return true;
        }
        return false;
    }
}