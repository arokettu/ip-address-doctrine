<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPAddressType;
use Arokettu\IP\Doctrine\IPv4AddressType;
use Arokettu\IP\Doctrine\IPv6AddressType;
use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet4Type;
use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\InetType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use PHPUnit\Framework\TestCase;

class IPAddressTest extends TestCase
{
    public function testBindingType(): void
    {
        $type  = new IPAddressType();
        $type4 = new IPv4AddressType();
        $type6 = new IPv6AddressType();

        $inet  = new InetType();
        $inet4 = new Inet4Type();
        $inet6 = new Inet6Type();

        self::assertEquals(ParameterType::STRING, $type->getBindingType());
        self::assertEquals(ParameterType::STRING, $type4->getBindingType());
        self::assertEquals(ParameterType::STRING, $type6->getBindingType());

        self::assertEquals(ParameterType::STRING, $inet->getBindingType());
        self::assertEquals(ParameterType::STRING, $inet4->getBindingType());
        self::assertEquals(ParameterType::STRING, $inet6->getBindingType());
    }

    public function testCreation(): void
    {
        $types = [IPAddressType::class, IPv4AddressType::class, IPv6AddressType::class];

        $sql = [
            [new SQLitePlatform(),      ['VARCHAR(39)',     'VARCHAR(15)',  'VARCHAR(39)']],
            [new MySQLPlatform(),       ['VARCHAR(39)',     'VARCHAR(15)',  'VARCHAR(39)']],
            [new PostgreSQLPlatform(),  ['VARCHAR(39)',     'VARCHAR(15)',  'VARCHAR(39)']],
            [new MariaDBPlatform(),     ['VARCHAR(39)',     'VARCHAR(15)',  'VARCHAR(39)']],
            [new SQLServerPlatform(),   ['NVARCHAR(39)',    'NVARCHAR(15)', 'NVARCHAR(39)']],
            [new OraclePlatform(),      ['VARCHAR2(39)',    'VARCHAR2(15)', 'VARCHAR2(39)']],
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

    public function testCreationPlatformSpecific(): void
    {
        $maria = new MariaDBPlatform();
        $pg = new PostgreSQLPlatform();

        $inet  = new InetType();
        $inet4 = new Inet4Type();
        $inet6 = new Inet6Type();

        $column = ['name' => 'test_test'];

        self::assertEquals('inet', $inet->getSQLDeclaration($column, $pg));
        self::assertEquals('inet4', $inet4->getSQLDeclaration($column, $maria));
        self::assertEquals('inet6', $inet6->getSQLDeclaration($column, $maria));
    }
}
