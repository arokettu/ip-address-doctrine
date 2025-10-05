<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

use Arokettu\IP\Doctrine\Demo\MySqlIP;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPBlock;

['db' => $db, 'em' => $em] = require __DIR__ . '/db.php';

$ip1 = new MySqlIP();

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

/** @var MySqlIP $ip1_found */
$ip1_found = $em->find(MySqlIP::class, $id1);

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

$ip2 = new MySqlIP();

$ip2->ip = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_bin = IPAddress::fromString('2001:ffff::ffff:abcd');
$ip2->ip_block = IPBlock::fromString('2001:ffff::ffff:abcd/64');
$ip2->ip_block_bin = IPBlock::fromString('2001:ffff::ffff:abcd/100');

$em->persist($ip2);
$em->flush();
$id2 = $ip2->id;
$em->detach($ip2);

/** @var MySqlIP $ip2_found */
$ip2_found = $em->find(MySqlIP::class, $id2);

var_dump((string)$ip2_found->ip);
var_dump((string)$ip2_found->ip_bin);
var_dump((string)$ip2_found->ip_block);
var_dump((string)$ip2_found->ip_block_bin);

echo '--------' . PHP_EOL;

// ipv4-like

$ip3 = new MySqlIP();

$ip3->ip = IPAddress::fromString('::ffff:abcd:ef01');

$em->persist($ip3);
$em->flush();
$id3 = $ip3->id;
$em->detach($ip3);

/** @var MySqlIP $ip3_found */
$ip3_found = $em->find(MySqlIP::class, $id3);

var_dump((string)$ip3_found->ip);
