<?php
declare(strict_types=1);

return [
    "dbname" => getenv("DATABASE_NAME"),
    "user" => getenv("DATABASE_USER"),
    "password" => getenv("DATABASE_PASSWORD"),
    "host" => getenv("DATABASE_HOST"),
    "driver" => getenv("DATABASE_DRIVER"),
];