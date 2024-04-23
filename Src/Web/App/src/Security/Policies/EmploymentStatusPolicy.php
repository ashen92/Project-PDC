<?php
declare(strict_types=1);

namespace App\Security\Policies;

use App\Security\IPolicy;

class EmploymentStatusPolicy implements IPolicy
{
    public const Employed = 'employed';
    public const Unemployed = 'unemployed';

    public readonly string $status;

    public function __construct(string $status)
    {
        if ($status !== self::Employed && $status !== self::Unemployed) {
            throw new \InvalidArgumentException('Invalid employment status');
        }
        $this->status = $status;
    }
}