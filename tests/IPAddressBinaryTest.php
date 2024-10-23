<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPAddressBinaryType;
use Arokettu\IP\Doctrine\IPv4AddressBinaryType;
use Arokettu\IP\Doctrine\IPv6AddressBinaryType;
use Arokettu\IP\Doctrine\Tests\Helpers\TestHelper;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use PHPUnit\Framework\TestCase;

class IPAddressBinaryTest extends TestCase
{
    public function testBindingType(): void
    {
        $type  = new IPAddressBinaryType();
        $type4 = new IPv4AddressBinaryType();
        $type6 = new IPv6AddressBinaryType();

        self::assertEquals(ParameterType::BINARY, $type->getBindingType());
        self::assertEquals(ParameterType::BINARY, $type4->getBindingType());
        self::assertEquals(ParameterType::BINARY, $type6->getBindingType());
    }

    public function testCreation(): void
    {
        $types = [IPAddressBinaryType::class, IPv4AddressBinaryType::class, IPv6AddressBinaryType::class];

        $sql = [
            [new SQLitePlatform(),      ['BLOB',            'BLOB',         'BLOB']],
            [new MySQLPlatform(),       ['VARBINARY(16)',   'BINARY(4)',    'BINARY(16)']],
            [new PostgreSQLPlatform(),  ['BYTEA',           'BYTEA',        'BYTEA']],
            [new MariaDBPlatform(),     ['VARBINARY(16)',   'BINARY(4)',    'BINARY(16)']],
            [new SQLServerPlatform(),   ['VARBINARY(16)',   'BINARY(4)',    'BINARY(16)']],
            [new OraclePlatform(),      ['RAW(16)',         'RAW(4)',       'RAW(16)']],
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

    public function testStringOut(): void
    {
        $platform = new SQLitePlatform();

        $addr = new IPAddressBinaryType();
        $addr4 = new IPv4AddressBinaryType();
        $addr6 = new IPv6AddressBinaryType();

        $ipv4 = '162.58.94.238';
        $ipv6 = '4001:e7f9::45b7:010a';

        $ipv4php = IPv4Address::fromString($ipv4);
        $ipv6php = IPv6Address::fromString($ipv6);

        $ipv4bin = hex2bin('a23a5eee');
        $ipv6bin = hex2bin('4001e7f9000000000000000045b7010a');

        self::assertEquals($ipv4php, $addr->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $addr->convertToPHPValue($ipv6bin, $platform));

        self::assertEquals($ipv4php, $addr4->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $addr6->convertToPHPValue($ipv6bin, $platform));
    }

    public function testStreamOut(): void
    {
        $platform = new SQLitePlatform();

        $addr = new IPAddressBinaryType();
        $addr4 = new IPv4AddressBinaryType();
        $addr6 = new IPv6AddressBinaryType();

        $ipv4 = '162.58.94.238';
        $ipv6 = '4001:e7f9::45b7:010a';

        $ipv4php = IPv4Address::fromString($ipv4);
        $ipv6php = IPv6Address::fromString($ipv6);

        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a5eee'));
        $ipv6bin = TestHelper::stringToStream(hex2bin('4001e7f9000000000000000045b7010a'));

        self::assertEquals($ipv4php, $addr->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $addr->convertToPHPValue($ipv6bin, $platform));

        rewind($ipv4bin);
        rewind($ipv6bin);

        self::assertEquals($ipv4php, $addr4->convertToPHPValue($ipv4bin, $platform));
        self::assertEquals($ipv6php, $addr6->convertToPHPValue($ipv6bin, $platform));
    }

    public function testStreamWrongLength(): void
    {
        $platform = new SQLitePlatform();
        $addr = new IPAddressBinaryType();
        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a500014'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0xA23A500014" to Doctrine Type "arokettu_ip_bin".'
        );

        $addr->convertToPHPValue($ipv4bin, $platform);
    }

    public function testStreamWrongLength64(): void
    {
        $platform = new SQLitePlatform();
        $addr4 = new IPv4AddressBinaryType();
        $ipv6bin = TestHelper::stringToStream(hex2bin('4001e7f9000000000000000045b7010a'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0x4001E7F900" to Doctrine Type "arokettu_ipv4_bin".'
        );

        $addr4->convertToPHPValue($ipv6bin, $platform);
    }

    public function testStreamWrongLength46(): void
    {
        $platform = new SQLitePlatform();
        $addr6 = new IPv6AddressBinaryType();
        $ipv4bin = TestHelper::stringToStream(hex2bin('a23a5eee'));

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value "0xA23A5EEE" to Doctrine Type "arokettu_ipv6_bin".'
        );

        $addr6->convertToPHPValue($ipv4bin, $platform);
    }

    public function testStringIn(): void
    {
        $platform = new SQLitePlatform();

        $addr = new IPAddressBinaryType();
        $addr4 = new IPv4AddressBinaryType();
        $addr6 = new IPv6AddressBinaryType();

        $ipv4 = '162.58.94.238';
        $ipv6 = '4001:e7f9::45b7:010a';

        $ipv4bin = hex2bin('a23a5eee');
        $ipv6bin = hex2bin('4001e7f9000000000000000045b7010a');

        self::assertEquals($ipv4bin, $addr->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $addr->convertToDatabaseValue($ipv6, $platform));

        self::assertEquals($ipv4bin, $addr4->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $addr6->convertToDatabaseValue($ipv6, $platform));
    }

    public function testObjectIn(): void
    {
        $platform = new SQLitePlatform();

        $addr = new IPAddressBinaryType();
        $addr4 = new IPv4AddressBinaryType();
        $addr6 = new IPv6AddressBinaryType();

        $ipv4 = IPv4Address::fromString('162.58.94.238');
        $ipv6 = IPv6Address::fromString('4001:e7f9::45b7:010a');

        $ipv4bin = hex2bin('a23a5eee');
        $ipv6bin = hex2bin('4001e7f9000000000000000045b7010a');

        self::assertEquals($ipv4bin, $addr->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $addr->convertToDatabaseValue($ipv6, $platform));

        self::assertEquals($ipv4bin, $addr4->convertToDatabaseValue($ipv4, $platform));
        self::assertEquals($ipv6bin, $addr6->convertToDatabaseValue($ipv6, $platform));
    }
}
