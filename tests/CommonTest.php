<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPAddressType;
use Arokettu\IP\Doctrine\IPv6AddressType;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CommonTest extends TestCase
{
    public function testNull(): void
    {
        $type = new IPv6AddressType();
        $platform = new SQLitePlatform();

        self::assertNull($type->convertToDatabaseValue(null, $platform)); // null in
        self::assertNull($type->convertToPHPValue(null, $platform)); // null out
    }

    public function testObjectInWrongClass(): void
    {
        $type = new IPAddressType();
        $platform = new SQLitePlatform();

        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value of type stdClass to type arokettu_ip. ' .
            'Expected one of the following types: null, string, Arokettu\IP\IPv4Address, Arokettu\IP\IPv6Address.',
        );

        // non-ip, non-stringable class
        $type->convertToDatabaseValue(new stdClass(), $platform);
    }
}
