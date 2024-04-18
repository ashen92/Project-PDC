<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthorizationService
{
    /**
     * @var array<string, array<IPolicy, IPolicyHandler>>
     */
    private array $policyHandlers = [];

    public function __construct(
        private readonly AuthorizationRepository $authzRepo,
        private readonly SessionInterface $session,
    ) {
    }

    public function addPolicyHandler(string $policyName, IPolicy $policy, IPolicyHandler $handler): void
    {
        $this->policyHandlers[$policyName] = [$policy, $handler];
    }

    public function authorize(string $policyName): bool
    {
        $userId = (int) $this->session->get('user_id');
        $handler = $this->policyHandlers[$policyName][1];
        return $handler->handle($userId, $this->policyHandlers[$policyName][0]);
    }

    public function hasRole(string $role): bool
    {
        $userId = (int) $this->session->get('user_id');
        return $this->authzRepo->hasRole($userId, $role);
    }

    public function hasPermission(string $name): bool
    {
        $userId = (int) $this->session->get('user_id');
        return $this->authzRepo->hasPermission($userId, $name);
    }
}