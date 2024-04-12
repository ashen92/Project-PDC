<?php
declare(strict_types=1);

namespace Database\Entities;

use DB\Entities\Application;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'application_files')]
class ApplicationFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Application::class)]
    #[ORM\JoinColumn(name: 'application_id', referencedColumnName: 'id')]
    private Application $application;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $path;

    public function __construct(
        Application $application,
        string $name,
        string $path
    ) {
        $this->application = $application;
        $this->name = $name;
        $this->path = $path;
    }
}