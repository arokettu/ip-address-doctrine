<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPBlockType;
use Arokettu\IP\Doctrine\IPv4BlockType;
use Arokettu\IP\Doctrine\IPv6BlockType;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\CidrType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use PHPUnit\Framework\TestCase;

class IPBlockTest extends TestCase
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
                    $type::class . ' / ' . $platform::class
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
}
