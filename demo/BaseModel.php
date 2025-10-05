<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Doctrine\Demo;

use Arokettu\IP\AnyIPAddress;
use Arokettu\IP\AnyIPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
// phpcs:ignore SlevomatCodingStandard.Namespaces.DisallowGroupUse.DisallowedGroupUse
use Arokettu\IP\Doctrine\{
    IPAddressBinaryType, IPAddressType, IPBlockBinaryType, IPBlockType,
    IPv4AddressBinaryType, IPv4AddressType, IPv4BlockBinaryType, IPv4BlockType,
    IPv6AddressBinaryType, IPv6AddressType, IPv6BlockBinaryType, IPv6BlockType,
};
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

abstract class BaseModel
{
    #[Column, Id, GeneratedValue]
    public int $id;

    #[Column(type: IPAddressType::NAME, nullable: true)]
    public AnyIPAddress|null $ip;
    #[Column(type: IPv4AddressType::NAME, nullable: true)]
    public IPv4Address|null $ipv4;
    #[Column(type: IPv6AddressType::NAME, nullable: true)]
    public IPv6Address|null $ipv6;

    #[Column(type: IPBlockType::NAME, nullable: true)]
    public AnyIPBlock|null $ip_block;
    #[Column(type: IPv4BlockType::NAME, nullable: true)]
    public IPv4Block|null $ipv4_block;
    #[Column(type: IPv6BlockType::NAME, nullable: true)]
    public IPv6Block|null $ipv6_block;

    #[Column(type: IPAddressBinaryType::NAME, nullable: true)]
    public AnyIPAddress|null $ip_bin;
    #[Column(type: IPv4AddressBinaryType::NAME, nullable: true)]
    public IPv4Address|null $ipv4_bin;
    #[Column(type: IPv6AddressBinaryType::NAME, nullable: true)]
    public IPv6Address|null $ipv6_bin;

    #[Column(type: IPBlockBinaryType::NAME, nullable: true)]
    public AnyIPBlock|null $ip_block_bin;
    #[Column(type: IPv4BlockBinaryType::NAME, nullable: true)]
    public IPv4Block|null $ipv4_block_bin;
    #[Column(type: IPv6BlockBinaryType::NAME, nullable: true)]
    public IPv6Block|null $ipv6_block_bin;
}
