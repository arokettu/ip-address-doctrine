<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Tests;

use Arokettu\IP\Doctrine\IPBlockType;
use Arokettu\IP\Doctrine\IPv4BlockType;
use Arokettu\IP\Doctrine\IPv6BlockType;
use Arokettu\IP\Doctrine\VendorSpecific\PostgreSQL\CidrType;
use Doctrine\DBAL\ParameterType;
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
}
