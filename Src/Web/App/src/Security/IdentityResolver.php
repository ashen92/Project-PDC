<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class IdentityResolver implements ValueResolverInterface
{
    private $identityProvider;

    public function __construct(IdentityProvider $identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$request->hasSession() || !$request->getSession()->has('user_id')) {
            return [];
        }

        if ($argument->getType() !== Identity::class) {
            return [];
        }

        return [$this->identityProvider->getIdentity((int) $request->getSession()->get("user_id"))];
    }
}