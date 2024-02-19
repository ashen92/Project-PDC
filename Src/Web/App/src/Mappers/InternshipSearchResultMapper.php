<?php
declare(strict_types=1);

namespace App\Mappers;

use App\Interfaces\IMapper;

class InternshipSearchResultMapper implements IMapper
{
    /**
     * @throws \Exception
     */
    #[\Override] public static function map(array $row): mixed
    {
        $internship = InternshipMapper::map($row);
        $organizationName = $row['orgName'];
        $organizationLogoFilePath = $row['orgLogoFilePath'];
        return new \App\Models\InternshipSearchResult(
            $internship,
            $organizationName,
            $organizationLogoFilePath
        );
    }
}