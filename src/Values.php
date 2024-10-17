<?php

declare(strict_types=1);

namespace Arokettu\IP\Doctrine;

/**
 * @internal
 */
final class Values
{
    public const IPV4_LENGTH = 15; // '127.123.210.111'
    public const IPV6_LENGTH = 39; // '2001:0db8:0000:0000:0000:8a2e:0370:7334'

    public const IPV4_BYTES = 4;
    public const IPV6_BYTES = 16;

    public const IPV4_CIDR_LENGTH = 18; // '127.123.210.111/32'
    public const IPV6_CIDR_LENGTH = 43; // '2001:0db8:0000:0000:0000:8a2e:0370:7334/128'

    public const IPV4_CIDR_BYTES = 5;
    public const IPV6_CIDR_BYTES = 17;
}
