<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPBlockType;
use Arokettu\IP\Doctrine\IPv4BlockType;
use Arokettu\IP\Doctrine\IPv6BlockType;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\CidrType;
use Arokettu\IP\IPAddress;
use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use PHPUnit\Framework\TestCase;

final class IPBlockTest extends TestCase
{
    public function testBindingType(): void
    {
        $type  = new IPBlockType();
        $type4 = new IPv4BlockType();
        $type6 = new IPv6BlockType();

        $cidr = new CidrType();

        self::assertEquals(ParameterType::STRING, $type->getBindingType());
        self::assertEquals(ParameterType::STRING, $type4->getBindingType());
        self::assertEquals(ParameterType::STRING, $type6->getBindingType());

        self::assertEquals(ParameterType::STRING, $cidr->getBindingType());
    }

    public function testCreation(): void
    {
        $types = [IPBlockType::class, IPv4BlockType::class, IPv6BlockType::class];

        $sql = [
            [new SQLitePlatform(),      ['VARCHAR(43)',     'VARCHAR(18)',  'VARCHAR(43)']],
            [new MySQLPlatform(),       ['VARCHAR(43)',     'VARCHAR(18)',  'VARCHAR(43)']],
            [new PostgreSQLPlatform(),  ['VARCHAR(43)',     'VARCHAR(18)',  'VARCHAR(43)']],
            [new MariaDBPlatform(),     ['VARCHAR(43)',     'VARCHAR(18)',  'VARCHAR(43)']],
            [new SQLServerPlatform(),   ['NVARCHAR(43)',    'NVARCHAR(18)', 'NVARCHAR(43)']],
            [new OraclePlatform(),      ['VARCHAR2(43)',    'VARCHAR2(18)', 'VARCHAR2(43)']],
        ];

        $column = ['name' => 'test_test'];

        foreach ($types as $id => $class) {
            /** @var AbstractType $type */
            $type = new $class();
            foreach ($sql as [$platform, $query]) {
                self::assertEquals(
                    $query[$id],
                    $type->getSQLDeclaration($column, $platform),
                    $type::class . ' / ' . $platform::class,
                );
            }
        }
    }

    public function testCreationPlatformSpecific(): void
    {
        $pg = new PostgreSQLPlatform();

        $cidr = new CidrType();

        $column = ['name' => 'test_test'];

        self::assertEquals('cidr', $cidr->getSQLDeclaration($column, $pg));
    }

    public function testStringOut(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockType();
        $block4 = new IPv4BlockType();
        $block6 = new IPv6BlockType();

        $cidr = new CidrType();

        $ipv4db = '162.58.80.0/20'; // see it being normalized
        $ipv6db = '4001:e7f9::4000:0/100'; // see it being normalized

        $ipv4php = IPv4Block::fromString($ipv4db);
        $ipv6php = IPv6Block::fromString($ipv6db);

        self::assertEquals($ipv4php, $block->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $block->convertToPHPValue($ipv6db, $platform));

        self::assertEquals($ipv4php, $block4->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $block6->convertToPHPValue($ipv6db, $platform));

        self::assertEquals($ipv4php, $cidr->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $cidr->convertToPHPValue($ipv6db, $platform));
    }

    public function testObjectOut(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockType();
        $block4 = new IPv4BlockType();
        $block6 = new IPv6BlockType();

        $cidr = new CidrType();

        $ipv4db = IPv4Block::fromString('162.58.80.0/20');
        $ipv6db = IPv6Block::fromString('4001:e7f9::4000:0/100');

        $ipv4php = $ipv4db;
        $ipv6php = $ipv6db;

        self::assertEquals($ipv4php, $block->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $block->convertToPHPValue($ipv6db, $platform));

        self::assertEquals($ipv4php, $block4->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $block6->convertToPHPValue($ipv6db, $platform));

        self::assertEquals($ipv4php, $cidr->convertToPHPValue($ipv4db, $platform));
        self::assertEquals($ipv6php, $cidr->convertToPHPValue($ipv6db, $platform));
    }

    public function testStringIn(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockType();
        $block4 = new IPv4BlockType();
        $block6 = new IPv6BlockType();

        $cidr = new CidrType();

        $ipv4db = '162.58.80.0/20';
        $ipv6db = '4001:e7f9::4000:0/100';

        $ipv4in = '162.58.94.238/20'; // see it being normalized
        $ipv6in = '4001:e7f9::45b7:010a/100'; // see it being normalized

        self::assertEquals($ipv4db, $block->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $block->convertToDatabaseValue($ipv6in, $platform));

        self::assertEquals($ipv4db, $block4->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $block6->convertToDatabaseValue($ipv6in, $platform));

        self::assertEquals($ipv4db, $cidr->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $cidr->convertToDatabaseValue($ipv6in, $platform));
    }

    public function testObjectIn(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockType();
        $block4 = new IPv4BlockType();
        $block6 = new IPv6BlockType();

        $cidr = new CidrType();

        $ipv4db = '162.58.80.0/20';
        $ipv6db = '4001:e7f9::4000:0/100';

        $ipv4in = IPv4Block::fromString('162.58.94.238/20');
        $ipv6in = IPv6Block::fromString('4001:e7f9::45b7:010a/100');

        self::assertEquals($ipv4db, $block->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $block->convertToDatabaseValue($ipv6in, $platform));

        self::assertEquals($ipv4db, $block4->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $block6->convertToDatabaseValue($ipv6in, $platform));

        self::assertEquals($ipv4db, $cidr->convertToDatabaseValue($ipv4in, $platform));
        self::assertEquals($ipv6db, $cidr->convertToDatabaseValue($ipv6in, $platform));
    }

    public function testObjectInWrongClassIPBlockType(): void
    {
        $addr = new IPBlockType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv6Address" to "arokettu_ip_cidr". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv6Address. Expected types: Arokettu\IP\IPv4Block, Arokettu\IP\IPv6Block.',
        );

        $addr->convertToDatabaseValue(IPAddress::fromString('::1'), $platform);
    }

    public function testObjectInWrongClassIPv4BlockType(): void
    {
        $addr = new IPv4BlockType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv6Block" to "arokettu_ipv4_cidr". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv6Block. Expected type: Arokettu\IP\IPv4Block.',
        );

        $addr->convertToDatabaseValue(IPBlock::fromString('::1/128'), $platform);
    }

    public function testObjectInWrongClassIPv6BlockType(): void
    {
        $addr = new IPv6BlockType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv4Block" to "arokettu_ipv6_cidr". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv4Block. Expected type: Arokettu\IP\IPv6Block.',
        );

        $addr->convertToDatabaseValue(IPBlock::fromString('127.0.0.1/32'), $platform);
    }

    public function testObjectInWrongClassCidrType(): void
    {
        $addr = new CidrType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv6Address" to "arokettu_postgres_cidr". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv6Address. Expected types: Arokettu\IP\IPv4Block, Arokettu\IP\IPv6Block.',
        );

        $addr->convertToDatabaseValue(IPAddress::fromString('::1'), $platform);
    }
}
