<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

use Arokettu\IP\Doctrine\Demo\MariaDbIp;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPBlock;

['db' => $db, 'em' => $em] = require __DIR__ . '/db.php';

$ip1 = new MariaDbIp();

$ip1->ip = IPAddress::fromString('127.0.0.1');
$ip1->ip_bin = IPAddress::fromString('192.168.1.111');
$ip1->ipv4 = IPAddress::fromString('64.52.168.7');
$ip1->ipv4_bin = IPAddress::fromString('64.52.168.7');
$ip1->ipv6 = IPAddress::fromString('::1');
$ip1->ipv6_bin = IPAddress::fromString('2001::1');

$ip1->ip_block = IPBlock::fromString('127.0.0.1/32');
$ip1->ip_block_bin = IPBlock::fromString('192.168.1.111/24');
$ip1->ipv4_block = IPBlock::fromString('64.52.168.7/16');
$ip1->ipv4_block_bin = IPBlock::fromString('64.52.168.7/30');
$ip1->ipv6_block = IPBlock::fromString('::1/128');
$ip1->ipv6_block_bin = IPBlock::fromString('2001::ffff:1/116');

$em->persist($ip1);
$em->flush();
$id1 = $ip1->id;
$em->detach($ip1);

/** @var MariaDbIp $ip1_found */
$ip1_found = $em->find(MariaDbIp::class, $id1);

var_dump((string)$ip1_found->ip);
var_dump((string)$ip1_found->ip_bin);
var_dump((string)$ip1_found->ipv4);

var_dump((string)$ip1_found->ipv4_bin);
var_dump((string)$ip1_found->ipv6);
var_dump((string)$ip1_found->ipv6_bin);

var_dump((string)$ip1_found->ip_block);
var_dump((string)$ip1_found->ip_block_bin);
var_dump((string)$ip1_found->ipv4_block);

var_dump((string)$ip1_found->ipv4_block_bin);
var_dump((string)$ip1_found->ipv6_block);
var_dump((string)$ip1_found->ipv6_block_bin);

echo '--------' . PHP_EOL;

// store IPv6 in any ip

$ip2 = new MariaDbIp();

$ip2->ip = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_bin = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_block = IPBlock::fromString('2001:ffff::ffff:abcd/64');
$ip2->ip_block_bin = IPBlock::fromString('2001:ffff::ffff:abcd/100');

$em->persist($ip2);
$em->flush();
$id2 = $ip2->id;
$em->detach($ip2);

/** @var MariaDbIp $ip2_found */
$ip2_found = $em->find(MariaDbIp::class, $id2);

var_dump((string)$ip2_found->ip);
var_dump((string)$ip2_found->ip_bin);
var_dump((string)$ip2_found->ip_block);
var_dump((string)$ip2_found->ip_block_bin);

echo '--------' . PHP_EOL;

// ipv4-like

$ip3 = new MariaDbIp();

$ip3->ip = IPAddress::fromString('::ffff:abcd:ef01');

$em->persist($ip3);
$em->flush();
$id3 = $ip3->id;
$em->detach($ip3);

/** @var MariaDbIp $ip3_found */
$ip3_found = $em->find(MariaDbIp::class, $id3);

var_dump((string)$ip3_found->ip);

echo '--------' . PHP_EOL;

// native

$ip4 = new MariaDbIp();

$ip4->inet4 = IPAddress::fromString('32.58.245.89');
$ip4->inet6 = IPAddress::fromString('::ffff:32.58.245.89');

$em->persist($ip4);
$em->flush();
$id4 = $ip4->id;
$em->detach($ip4);

/** @var MariaDbIp $ip4_found */
$ip4_found = $em->find(MariaDbIp::class, $id4);

var_dump((string)$ip4_found->inet4);
var_dump((string)$ip4_found->inet6);

// also native query

$result = $db->executeQuery('SELECT inet4, inet6 FROM ip_test WHERE id = :id', ['id' => $id4])->fetchAssociative();
var_dump($result);
