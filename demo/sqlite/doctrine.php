<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

['db' => $db, 'em' => $em] = require __DIR__ . '/db.php';

$commands = [
];

ConsoleRunner::run(
    new SingleManagerProvider($em),
    $commands,
);
