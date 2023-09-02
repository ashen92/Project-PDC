<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Reference;

$container->register("repository.user", App\Repositories\UserRepository::class)
    ->setArguments([new Reference("database.connection")]);

$container->register("repository.internship", App\Repositories\InternshipRepository::class);