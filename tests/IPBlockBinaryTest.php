<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPBlockBinaryType;
use Arokettu\IP\Doctrine\IPv4BlockBinaryType;
use Arokettu\IP\Doctrine\IPv6BlockBinaryType;
use Doctrine\DBAL\ParameterType;
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
}
