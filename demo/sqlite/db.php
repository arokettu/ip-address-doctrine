<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

use Arokettu\IP\Doctrine\IPAddressBinaryType;
use Arokettu\IP\Doctrine\IPAddressType;
use Arokettu\IP\Doctrine\IPBlockBinaryType;
use Arokettu\IP\Doctrine\IPBlockType;
use Arokettu\IP\Doctrine\IPv4AddressBinaryType;
use Arokettu\IP\Doctrine\IPv4AddressType;
use Arokettu\IP\Doctrine\IPv4BlockBinaryType;
use Arokettu\IP\Doctrine\IPv4BlockType;
use Arokettu\IP\Doctrine\IPv6AddressBinaryType;
use Arokettu\IP\Doctrine\IPv6AddressType;
use Arokettu\IP\Doctrine\IPv6BlockBinaryType;
use Arokettu\IP\Doctrine\IPv6BlockType;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/model/SqliteIP.php';

Type::addType(IPAddressType::NAME, IPAddressType::class);
Type::addType(IPv4AddressType::NAME, IPv4AddressType::class);
Type::addType(IPv6AddressType::NAME, IPv6AddressType::class);

Type::addType(IPAddressBinaryType::NAME, IPAddressBinaryType::class);
Type::addType(IPv4AddressBinaryType::NAME, IPv4AddressBinaryType::class);
Type::addType(IPv6AddressBinaryType::NAME, IPv6AddressBinaryType::class);

Type::addType(IPBlockType::NAME, IPBlockType::class);
Type::addType(IPv4BlockType::NAME, IPv4BlockType::class);
Type::addType(IPv6BlockType::NAME, IPv6BlockType::class);

Type::addType(IPBlockBinaryType::NAME, IPBlockBinaryType::class);
Type::addType(IPv4BlockBinaryType::NAME, IPv4BlockBinaryType::class);
Type::addType(IPv6BlockBinaryType::NAME, IPv6BlockBinaryType::class);

$eventManager = new EventManager();

$options = [
    'driver' => 'pdo_sqlite',
    'path' => realpath(__DIR__ . '/..') . '/file.sqlite',
];
$db = DriverManager::getConnection($options);

$ormConfig = new Configuration();
$ormConfig->setProxyDir(__DIR__ . '/../tmp/proxy');
$ormConfig->setProxyNamespace('Proxy');
$ormConfig->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/model']));

$em = new EntityManager($db, $ormConfig, $eventManager);

return compact('db', 'em');
