<?php
declare(strict_types=1);

namespace DB\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'internship_program_settings')]
class InternshipProgramSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $settingKey;

    #[ORM\Column(type: 'string')]
    private string $settingValue;

    #[ORM\Column(type: 'string')]
    private string $settingValueType;

    #[ORM\Column(type: 'text')]
    private string $description;

    public function __construct(
        string $settingKey,
        string $settingValue,
        string $settingValueType,
        string $description
    ) {
        $this->settingKey = $settingKey;
        $this->settingValue = $settingValue;
        $this->settingValueType = $settingValueType;
        $this->description = $description;
    }
}