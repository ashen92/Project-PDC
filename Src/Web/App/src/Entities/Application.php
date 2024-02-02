<?php
declare(strict_types=1);

namespace App\Entities;

use App\Models\Application\Status;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'applications')]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Internship::class)]
    #[ORM\JoinColumn(name: 'internship_id', referencedColumnName: 'id')]
    private Internship $internship;

    #[ORM\Column(type: 'application_status')]
    private Status $status;

    public function __construct(
        Internship $internship,
        Status $status
    ) {
        $this->internship = $internship;
        $this->status = $status;
    }
}