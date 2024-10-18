<?php

declare(strict_types=1);

use Arokettu\IP\Doctrine\Demo\PostgresIP;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPBlock;

['db' => $db, 'em' => $em] = require __DIR__ . '/db.php';

$ip1 = new PostgresIP();

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

/** @var PostgresIP $ip1_found */
$ip1_found = $em->find(PostgresIP::class, $id1);

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

$ip2 = new PostgresIP();

$ip2->ip = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_bin = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_block = IPBlock::fromString('2001:ffff::ffff:abcd/64');
$ip2->ip_block_bin = IPBlock::fromString('2001:ffff::ffff:abcd/100');

$em->persist($ip2);
$em->flush();
$id2 = $ip2->id;
$em->detach($ip2);

/** @var PostgresIP $ip2_found */
$ip2_found = $em->find(PostgresIP::class, $id2);

var_dump((string)$ip2_found->ip);
var_dump((string)$ip2_found->ip_bin);
var_dump((string)$ip2_found->ip_block);
var_dump((string)$ip2_found->ip_block_bin);

echo '--------' . PHP_EOL;

// ipv4-like

$ip3 = new PostgresIP();

$ip3->ip = IPAddress::fromString('::ffff:abcd:ef01');

$em->persist($ip3);
$em->flush();
$id3 = $ip3->id;
$em->detach($ip3);

/** @var PostgresIP $ip3_found */
$ip3_found = $em->find(PostgresIP::class, $id3);

var_dump((string)$ip3_found->ip);

echo '--------' . PHP_EOL;

// native v4

$ip4 = new PostgresIP();

$ip4->inet = IPAddress::fromString('32.58.245.89');
$ip4->cidr = IPBlock::fromString('32.58.245.89/24');

$em->persist($ip4);
$em->flush();
$id4 = $ip4->id;
$em->detach($ip4);

/** @var PostgresIP $ip4_found */
$ip4_found = $em->find(PostgresIP::class, $id4);

var_dump((string)$ip4_found->inet);
var_dump((string)$ip4_found->cidr);

// also native query

$result = $db->executeQuery('SELECT inet, cidr FROM ip_test WHERE id = :id', ['id' => $id4])->fetchAssociative();
var_dump($result);

echo '--------' . PHP_EOL;

// native v6

$ip5 = new PostgresIP();

$ip5->inet = IPAddress::fromString('2001:ffff::face:b00c:1');
$ip5->cidr = IPBlock::fromString('2001:ffff::face:b00c:1/100');

$em->persist($ip5);
$em->flush();
$id5 = $ip5->id;
$em->detach($ip5);

/** @var PostgresIP $ip5_found */
$ip5_found = $em->find(PostgresIP::class, $id5);

var_dump((string)$ip5_found->inet);
var_dump((string)$ip5_found->cidr);

// also native query

$result = $db->executeQuery('SELECT inet, cidr FROM ip_test WHERE id = :id', ['id' => $id5])->fetchAssociative();
var_dump($result);

// native v4 mapped

$ip6 = new PostgresIP();

$ip6->inet = IPAddress::fromString('::ffff:127.0.0.1');
$ip6->cidr = IPBlock::fromString('::ffff:127.0.0.1/102');

$em->persist($ip6);
$em->flush();
$id6 = $ip6->id;
$em->detach($ip6);

/** @var PostgresIP $ip6_found */
$ip6_found = $em->find(PostgresIP::class, $id6);

var_dump((string)$ip6_found->inet);
var_dump((string)$ip6_found->cidr);

// also native query

$result = $db->executeQuery('SELECT inet, cidr FROM ip_test WHERE id = :id', ['id' => $id6])->fetchAssociative();
var_dump($result);
