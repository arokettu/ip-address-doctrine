<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPv6AddressType;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\TestCase;

class CommonTest extends TestCase
{
    public function testNull(): void
    {
        $type = new IPv6AddressType();
        $platform = new SQLitePlatform();

        self::assertNull($type->convertToDatabaseValue(null, $platform)); // null in
        self::assertNull($type->convertToPHPValue(null, $platform)); // null out
    }
}
