<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Demo;

use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet4Type;
use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

require __DIR__ . '/../../BaseModel.php';

#[Entity, Table(name: 'ip_test')]
class MariaDbIp extends BaseModel
{
    #[Column(type: Inet4Type::NAME, nullable: true)]
    public IPv4Address|null $inet4;

    #[Column(type: Inet6Type::NAME, nullable: true)]
    public IPv6Address|null $inet6;
}
