<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPAddressBinaryType;
use Arokettu\IP\Doctrine\IPv4AddressBinaryType;
use Arokettu\IP\Doctrine\IPv6AddressBinaryType;
use Doctrine\DBAL\ParameterType;
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
}
