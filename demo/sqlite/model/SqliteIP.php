<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Demo;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

require __DIR__ . '/../../BaseModel.php';

#[Entity, Table(name: 'ip_test')]
class SqliteIP extends BaseModel
{
}
