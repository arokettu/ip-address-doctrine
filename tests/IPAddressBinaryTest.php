<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\AbstractType;
use Arokettu\IP\Doctrine\IPAddressBinaryType;
use Arokettu\IP\Doctrine\IPv4AddressBinaryType;
use Arokettu\IP\Doctrine\IPv6AddressBinaryType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
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
}
