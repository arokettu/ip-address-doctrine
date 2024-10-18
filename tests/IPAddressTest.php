<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPAddressType;
use Arokettu\IP\Doctrine\IPv4AddressType;
use Arokettu\IP\Doctrine\IPv6AddressType;
use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet4Type;
use Arokettu\IP\Doctrine\VendorSpecific\MariaDB\Inet6Type;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\InetType;
use Doctrine\DBAL\ParameterType;
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
}
