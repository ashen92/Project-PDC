<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\DTOs\CreateInternshipCycleDTO;
use App\DTOs\CreateUserDTO;
use App\Entities\InternshipCycle;
use App\Exceptions\UserExistsException;

interface IInternshipCycleService
{
    public function getEligibleStudentGroupsForInternshipCycle(): array;
    public function getEligiblePartnerGroupsForInternshipCycle(): array;
    public function getLatestInternshipCycleId(): ?int;
    public function getLatestInternshipCycle(): ?InternshipCycle;
    public function getLatestActiveInternshipCycle(): ?InternshipCycle;
    public function createInternshipCycle(CreateInternshipCycleDTO $createInternshipCycleDTO): InternshipCycle;
    public function endInternshipCycle(?int $id = null): bool;
    public function getStudentUsers(?int $internshipCycleId = null): array;

    /**
     * @throws UserExistsException If a user with the same email already exists
     */
    public function createManagedUser(int $ownerId, CreateUserDTO $userDTO): void;
}