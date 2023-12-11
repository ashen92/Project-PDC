<?php
declare(strict_types=1);

namespace App\Repositories;

use Doctrine\ORM\EntityManagerInterface;

abstract class Repository
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }
}