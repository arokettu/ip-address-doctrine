<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPBlockBinaryType;
use Arokettu\IP\Doctrine\IPv4BlockBinaryType;
use Arokettu\IP\Doctrine\IPv6BlockBinaryType;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use PHPUnit\Framework\TestCase;

class IPBlockBinaryTest extends TestCase
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
                    $type::class . ' / ' . $platform::class
                );
            }
        }
    }

    public function testValueOut(): void
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
}
