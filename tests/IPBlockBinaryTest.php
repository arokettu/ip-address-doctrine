<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPBlockBinaryType;
use Arokettu\IP\Doctrine\IPv4BlockBinaryType;
use Arokettu\IP\Doctrine\IPv6BlockBinaryType;
use Arokettu\IP\Doctrine\Tests\Helpers\TestHelper;
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
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use PHPUnit\Framework\TestCase;

final class IPBlockBinaryTest extends TestCase
{
    public function testBindingType(): void
    {
        $type  = new IPBlockBinaryType();
        $type4 = new IPv4BlockBinaryType();
        $type6 = new IPv6BlockBinaryType();

        self::assertEquals(ParameterType::BINARY, $type->getBindingType());
        self::assertEquals(ParameterType::BINARY, $type4->getBindingType());
        self::assertEquals(ParameterType::BINARY, $type6->getBindingType());
    }

    public function testCreation(): void
    {
        $types = [IPBlockBinaryType::class, IPv4BlockBinaryType::class, IPv6BlockBinaryType::class];

        $sql = [
            [new SQLitePlatform(),      ['BLOB',            'BLOB',         'BLOB']],
            [new MySQLPlatform(),       ['VARBINARY(17)',   'BINARY(5)',    'BINARY(17)']],
            [new PostgreSQLPlatform(),  ['BYTEA',           'BYTEA',        'BYTEA']],
            [new MariaDBPlatform(),     ['VARBINARY(17)',   'BINARY(5)',    'BINARY(17)']],
            [new SQLServerPlatform(),   ['VARBINARY(17)',   'BINARY(5)',    'BINARY(17)']],
            [new OraclePlatform(),      ['RAW(17)',         'RAW(5)',       'RAW(17)']],
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

    public function testStringOut(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockBinaryType();
        $block4 = new IPv4BlockBinaryType();
        $block6 = new IPv6BlockBinaryType();

        $ipv4 = '162.58.80.0/20';
        $ipv6 = '4001:e7f9::4000:0/100';

        $ipv4php = IPv4Block::fromString($ipv4, strict: true);
        $ipv6php = IPv6Block::fromString($ipv6, strict: true);

        $ipv4bin = hex2bin('a23a500014');
        $ipv6bin = hex2bin('4001e7f900000000000000004000000064');

        self::assertEquals($ipv4php, $block->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block->convertToPHPValue($ipv6bin, $platform));

        self::assertEquals($ipv4php, $block4->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block6->convertToPHPValue($ipv6bin, $platform));
    }

    public function testStreamOut(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockBinaryType();
        $block4 = new IPv4BlockBinaryType();
        $block6 = new IPv6BlockBinaryType();

        $ipv4 = '162.58.80.0/20';
        $ipv6 = '4001:e7f9::4000:0/100';

        $ipv4php = IPv4Block::fromString($ipv4, strict: true);
        $ipv6php = IPv6Block::fromString($ipv6, strict: true);

        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a500014'));
        $ipv6bin = TestHelper::stringToStream(hex2bin('4001e7f900000000000000004000000064'));

        self::assertEquals($ipv4php, $block->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block->convertToPHPValue($ipv6bin, $platform));

        rewind($ipv4bin);
        rewind($ipv6bin);

        self::assertEquals($ipv4php, $block4->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block6->convertToPHPValue($ipv6bin, $platform));
    }

    public function testStreamWrongLength(): void
    {
        $platform = new SQLitePlatform();
        $block = new IPBlockBinaryType();
        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a5eee'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0xA23A5EEE" to Doctrine Type "arokettu_ip_cidr_bin".',
        );

        $block->convertToPHPValue($ipv4bin, $platform);
    }

    public function testStreamWrongLength64(): void
    {
        $platform = new SQLitePlatform();
        $block4 = new IPv4BlockBinaryType();
        $ipv6bin = TestHelper::stringToStream(hex2bin('4001e7f900000000000000004000000064'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0x4001E7F90000" to Doctrine Type "arokettu_ipv4_cidr_bin".',
        );

        $block4->convertToPHPValue($ipv6bin, $platform);
    }

    public function testStreamWrongLength46(): void
    {
        $platform = new SQLitePlatform();
        $block6 = new IPv6BlockBinaryType();
        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a500014'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0xA23A500014" to Doctrine Type "arokettu_ipv6_cidr_bin".',
        );

        $block6->convertToPHPValue($ipv4bin, $platform);
    }

    public function testStreamWrongPrefix(): void
    {
        $platform = new SQLitePlatform();
        $block4 = new IPv4BlockBinaryType();
        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a5000ff'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0xA23A5000FF" to Doctrine Type "arokettu_ipv4_cidr_bin".',
        );

        $block4->convertToPHPValue($ipv4bin, $platform);
    }

    public function testObjectOut(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockBinaryType();
        $block4 = new IPv4BlockBinaryType();
        $block6 = new IPv6BlockBinaryType();

        $ipv4 = '162.58.80.0/20';
        $ipv6 = '4001:e7f9::4000:0/100';

        $ipv4php = IPv4Block::fromString($ipv4, strict: true);
        $ipv6php = IPv6Block::fromString($ipv6, strict: true);

        $ipv4bin = $ipv4php;
        $ipv6bin = $ipv6php;

        self::assertEquals($ipv4php, $block->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block->convertToPHPValue($ipv6bin, $platform));

        self::assertEquals($ipv4php, $block4->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $block6->convertToPHPValue($ipv6bin, $platform));
    }

    public function testStringIn(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockBinaryType();
        $block4 = new IPv4BlockBinaryType();
        $block6 = new IPv6BlockBinaryType();

        $ipv4 = '162.58.94.238/20'; // see it being normalized
        $ipv6 = '4001:e7f9::45b7:010a/100'; // see it being normalized

        $ipv4bin = hex2bin('a23a500014');
        $ipv6bin = hex2bin('4001e7f900000000000000004000000064');

        self::assertEquals($ipv4bin, $block->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $block->convertToDatabaseValue($ipv6, $platform));

        self::assertEquals($ipv4bin, $block4->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $block6->convertToDatabaseValue($ipv6, $platform));
    }

    public function testObjectIn(): void
    {
        $platform = new SQLitePlatform();

        $block = new IPBlockBinaryType();
        $block4 = new IPv4BlockBinaryType();
        $block6 = new IPv6BlockBinaryType();

        $ipv4 = IPv4Block::fromString('162.58.94.238/20');
        $ipv6 = IPv6Block::fromString('4001:e7f9::45b7:010a/100');

        $ipv4bin = hex2bin('a23a500014');
        $ipv6bin = hex2bin('4001e7f900000000000000004000000064');

        self::assertEquals($ipv4bin, $block->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $block->convertToDatabaseValue($ipv6, $platform));

        self::assertEquals($ipv4bin, $block4->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $block6->convertToDatabaseValue($ipv6, $platform));
    }

    public function testObjectInWrongClassIPBlockType(): void
    {
        $addr = new IPBlockBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv6Address" to "arokettu_ip_cidr_bin". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv6Address. Expected types: Arokettu\IP\IPv4Block, Arokettu\IP\IPv6Block.',
        );

        $addr->convertToDatabaseValue(IPAddress::fromString('::1'), $platform);
    }

    public function testObjectInWrongClassIPv4BlockType(): void
    {
        $addr = new IPv4BlockBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv6Block" to "arokettu_ipv4_cidr_bin". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv6Block. Expected type: Arokettu\IP\IPv4Block.',
        );

        $addr->convertToDatabaseValue(IPBlock::fromString('::1/128'), $platform);
    }

    public function testObjectInWrongClassIPv6BlockType(): void
    {
        $addr = new IPv6BlockBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "Arokettu\IP\IPv4Block" to "arokettu_ipv6_cidr_bin". ' .
            'An error was triggered by the serialization: ' .
            'Unsupported type Arokettu\IP\IPv4Block. Expected type: Arokettu\IP\IPv6Block.',
        );

        $addr->convertToDatabaseValue(IPBlock::fromString('127.0.0.1/32'), $platform);
    }
}
