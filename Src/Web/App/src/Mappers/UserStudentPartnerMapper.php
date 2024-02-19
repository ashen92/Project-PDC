<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;
use App\Models\Partner;
use App\Models\Student;
use App\Models\User;

class UserStudentPartnerMapper implements IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] static function map(array $row): User|Student|Partner
    {
        $row['id'] = $row['user_id'];

        if ($row['type'] === 'student') {
            return StudentMapper::map($row);
        }
        if ($row['type'] === 'partner') {
            return PartnerMapper::map($row);
        }
        return UserMapper::map($row);
    }
}