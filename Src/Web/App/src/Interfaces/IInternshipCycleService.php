<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateInternshipCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\InternshipCycle;
use App\Exceptions\UserExistsException;

interface IInternshipCycleService
{
    /**
     * @return array<\App\Entities\UserGroup>
     */
    public function getEligibleStudentGroupsForInternshipCycle(): array;

    /**
     * @return array<\App\Entities\UserGroup>
     */
    public function getEligiblePartnerGroupsForInternshipCycle(): array;

    public function getLatestInternshipCycleId(): ?int;
    public function getLatestCycle(): ?\App\Models\InternshipCycle;
    public function getLatestActiveInternshipCycle(): ?InternshipCycle;
    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle;
    public function endInternshipCycle(?int $id = null): bool;

    /**
     * @return array<\App\Entities\Student>
     */
    public function getStudentUsers(?int $internshipCycleId = null): array;

    /**
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createManagedUser(int $ownerId, CreateUserDTO $userDTO): void;
}