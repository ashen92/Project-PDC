<?php
declare(strict_types=1);

namespace App\ValueResolver;

use App\Models\InternshipCycle;
use App\Repositories\InternshipProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class InternshipCycleResolver implements ValueResolverInterface
{
    public function __construct(
        private InternshipProgramRepository $internshipProgramRepository
    ) {

    }

    function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== InternshipCycle::class) {
            return [];
        }

        return [$this->internshipProgramRepository->findLatestActiveCycle()];
    }
}