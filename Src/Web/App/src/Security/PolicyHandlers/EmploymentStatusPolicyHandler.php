<?php
declare(strict_types=1);

namespace App\Security\PolicyHandlers;

use App\Repositories\InternMonitoringRepository;
use App\Security\IPolicyHandler;
use App\Security\Policies\EmploymentStatusPolicy;

class EmploymentStatusPolicyHandler implements IPolicyHandler
{
    public function __construct(
        private readonly InternMonitoringRepository $internMonitoringRepo,
    ) {
    }

    /**
     * @param EmploymentStatusPolicy $policy
     */
    function handle(int $userId, $policy): bool
    {
        $isEmployed = $this->internMonitoringRepo->isEmployed($userId);
        if ($policy->status === EmploymentStatusPolicy::Employed && $isEmployed) {
            return true;
        }
        if ($policy->status === EmploymentStatusPolicy::Unemployed && !$isEmployed) {
            return true;
        }
        return false;
    }
}