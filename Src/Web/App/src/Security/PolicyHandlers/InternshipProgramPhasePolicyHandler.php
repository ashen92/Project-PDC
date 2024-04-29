<?php
declare(strict_types=1);

namespace App\Security\PolicyHandlers;

use App\Repositories\InternshipProgramRepository;
use App\Security\IPolicyHandler;
use App\Security\Policies\InternshipProgramPhasePolicy;

readonly class InternshipProgramPhasePolicyHandler implements IPolicyHandler
{
    public function __construct(
        private InternshipProgramRepository $internshipProgramRepo,
    ) {
    }

    /**
     * @param InternshipProgramPhasePolicy $policy
     */
    function handle(int $userId, $policy): bool
    {
        $cycle = $this->internshipProgramRepo->findLatestActiveCycle();
        if ($policy->phase === InternshipProgramPhasePolicy::JobCollectionPhase && $cycle->isJobCollectionPhase()) {
            return true;
        }
        if ($policy->phase === InternshipProgramPhasePolicy::FirstRoundPhase && $cycle->isFirstRoundPhase()) {
            return true;
        }
        if ($policy->phase === InternshipProgramPhasePolicy::SecondRoundPhase && $cycle->isSecondRoundPhase()) {
            return true;
        }
        return false;
    }
}